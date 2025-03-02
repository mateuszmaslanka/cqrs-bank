<?php

namespace App\Infrastructure\Repository;

use App\Domain\Model\BankAccount;
use App\Domain\Repository\BankAccountCreator;
use App\Domain\Repository\BankAccountProvider;
use App\Domain\ValueObject\AccountNumber;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

class BankAccountInMemoryRepository implements BankAccountProvider, BankAccountCreator
{
    private array $bankAccounts = [];
    private Session $session;

    public function __construct(RequestStack $requestStack)
    {
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

    public function createNewBankAccount(int $id, AccountNumber $accountNumber): void
    {
        $this->bankAccounts[$id] = new BankAccount($id, $accountNumber);
        $this->save();
    }
}
