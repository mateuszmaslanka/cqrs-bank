<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\BankAccount;
use App\Domain\ValueObject\AccountNumber;

interface BankAccountProvider
{
    /**
     * @return BankAccount[]
     */
    public function listAllBankAccounts(): array;

    public function findOneBankAccountById(int $id): BankAccount;

    public function findOneBankAccountByAccountNumber(AccountNumber $accountNumber): BankAccount;
}
