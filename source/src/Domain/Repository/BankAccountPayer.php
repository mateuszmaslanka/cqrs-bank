<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\ValueObject\AccountNumber;

interface BankAccountPayer
{
    public function payFromBankAccount(AccountNumber $accountNumber, int $amount): void;
}
