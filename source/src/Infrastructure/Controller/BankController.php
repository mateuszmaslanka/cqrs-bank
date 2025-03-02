<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Application\Bus\CommandBus;
use App\Application\Bus\QueryBus;
use App\Application\Command\CreateBankAccount\CreateBankAccount;
use App\Application\Command\DepositIntoBankAccount\DepositIntoBankAccount;
use App\Application\Query\GetBankAccountById\GetBankAccountById;
use App\Application\Query\GetBankAccounts\GetBankAccounts;
use App\Domain\Model\BankAccount;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/bank', name: 'cqrs_bank')]
class BankController extends AbstractController
{
    public function __construct(
        private readonly QueryBus $queryBus,
        private readonly CommandBus $commandBus,
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
        $id = rand(1,999999);

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

        return $this->accountList();
    }

    #[Route('/account/{id}', name: '_account_details')]
    public function accountDetails(int $id): Response
    {
        $query = new GetBankAccountById($id);
        $bankAccount = $this->queryBus->query($query);

        return $this->render('bank/account/details.html.twig', [
            'bankAccount' => $bankAccount,
        ]);
    }
}
