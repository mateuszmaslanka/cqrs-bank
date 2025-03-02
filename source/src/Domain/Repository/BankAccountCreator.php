<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\ValueObject\AccountNumber;

interface BankAccountCreator
{
    public function createNewBankAccount(int $id, AccountNumber $accountNumber): void;
}
