<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Application\Bus\CommandBus;
use App\Application\Bus\QueryBus;
use App\Application\Command\CreateBankAccount\CreateBankAccount;
use App\Application\Command\DepositIntoBankAccount\DepositIntoBankAccount;
use App\Application\Command\PaymentFromBankAccount\PaymentFromBankAccount;
use App\Application\Query\GetBankAccountByAccountNumber\GetBankAccountByAccountNumber;
use App\Application\Query\GetBankAccountById\GetBankAccountById;
use App\Application\Query\GetBankAccounts\GetBankAccounts;
use App\Infrastructure\Cache\InSessionCache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/bank', name: 'cqrs_bank')]
class BankController extends AbstractController
{
    public function __construct(
        private readonly QueryBus $queryBus,
        private readonly CommandBus $commandBus,
        private readonly InSessionCache $cache,
    ) {
    }

    #[Route('/', name: '')]
    public function index(): Response
    {
        return $this->render('bank/index.html.twig');
    }

    #[Route('/account/list', name: '_account_list')]
    public function accountList(): Response
    {
        $query = new GetBankAccounts();
        $bankAccounts = $this->queryBus->query($query);

        return $this->render('bank/account/list.html.twig', [
            'bankAccounts' => $bankAccounts,
        ]);
    }

    #[Route('/account/create', name: '_account_create')]
    public function accountCreate(): Response
    {
        $id = rand(1, 999999);

        $command = new CreateBankAccount($id);
        $this->commandBus->dispatch($command);

        $query = new GetBankAccountById($id);
        $bankAccount = $this->queryBus->query($query);

        return $this->render('bank/account/create.html.twig', [
            'bankAccount' => $bankAccount,
        ]);
    }

    #[Route('/account/deposit/{accountNumber}/{amount}', name: '_account_deposit')]
    public function accountDeposit(string $accountNumber, int $amount): Response
    {
        $command = new DepositIntoBankAccount($accountNumber, $amount);
        $this->commandBus->dispatch($command);

        $cacheKey = $this->createBankAccountCacheKey($accountNumber);
        $this->cache->deleteItem($cacheKey);

        return $this->accountList();
    }

    #[Route('/account/pay/{accountNumber}/{amount}', name: '_account_pay')]
    public function accountPay(string $accountNumber, int $amount): Response
    {
        $command = new PaymentFromBankAccount($accountNumber, $amount);
        $this->commandBus->dispatch($command);

        $cacheKey = $this->createBankAccountCacheKey($accountNumber);
        $this->cache->deleteItem($cacheKey);

        return $this->accountList();
    }

    #[Route('/account/{accountNumber}', name: '_account_details')]
    public function accountDetails(string $accountNumber): Response
    {
        $cacheKey = $this->createBankAccountCacheKey($accountNumber);
        $cacheItem = $this->cache->getItem($cacheKey);
        $isCacheHit = $cacheItem->isHit();

        if (!$isCacheHit) {
            $query = new GetBankAccountByAccountNumber($accountNumber);
            $bankAccount = $this->queryBus->query($query);

            $cacheItem->set($bankAccount);
            $cacheItem->expiresAfter(30);
            $this->cache->save($cacheItem);
        }

        return $this->render('bank/account/details.html.twig', [
            'bankAccount' => $cacheItem->get(),
            'isCacheHit' => $isCacheHit,
            'cacheExpiresAt' => $cacheItem->getExpiresAt(),
        ]);
    }

    private function createBankAccountCacheKey(string $accountNumber): string
    {
        return sprintf('bank_account_%s', $accountNumber);
    }
}
