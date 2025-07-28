<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Model\BankAccount;
use App\Domain\Repository\BankAccountCreator;
use App\Domain\Repository\BankAccountDepositor;
use App\Domain\Repository\BankAccountPayer;
use App\Domain\Repository\BankAccountProvider;
use App\Domain\ValueObject\AccountNumber;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class BankAccountInMemoryRepository implements BankAccountProvider, BankAccountCreator, BankAccountDepositor, BankAccountPayer
{
    /** $var BankAccount[] */
    private array $bankAccounts = [];
    private SessionInterface $session;

    public function __construct(
        RequestStack $requestStack,
        private readonly TransactionMysqlRepository $tansactionRepository,
    ) {
        $this->session = $requestStack->getSession();
        $this->restore();
    }

    private function save(): void
    {
        $this->session->set('accounts', $this->bankAccounts);
    }

    private function restore(): void
    {
        $this->bankAccounts = $this->session->get('accounts', []);
    }

    public function listAllBankAccounts(): array
    {
        return $this->bankAccounts;
    }

    public function findOneBankAccountById(int $id): BankAccount
    {
        return $this->bankAccounts[$id]
            ?? throw new \RuntimeException('Account not found');
    }

    public function findOneBankAccountByAccountNumber(AccountNumber $accountNumber): BankAccount
    {
        foreach ($this->bankAccounts as $bankAccount) {
            if ($bankAccount->getAccountNumber()->equals($accountNumber)) {
                return $bankAccount;
            }
        }

        throw new \RuntimeException('Account not found');
    }

    public function createNewBankAccount(int $id, AccountNumber $accountNumber): void
    {
        $this->bankAccounts[$id] = new BankAccount($id, $accountNumber);
        $this->save();
    }

    public function depositIntoBankAccount(AccountNumber $accountNumber, int $amount): void
    {
        $bankAccount = $this->findOneBankAccountByAccountNumber($accountNumber);
        $bankAccount->deposite($amount);

        $this->tansactionRepository->addTransaction(
            $bankAccount->getId(),
            $amount,
        );
    }

    public function payFromBankAccount(AccountNumber $accountNumber, int $amount): void
    {
        $bankAccount = $this->findOneBankAccountByAccountNumber($accountNumber);
        $bankAccount->pay($amount);

        $this->tansactionRepository->addTransaction(
            $bankAccount->getId(),
            -$amount,
        );
    }
}
