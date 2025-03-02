<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\ValueObject\AccountNumber;

interface BankAccountDepositor
{
    public function depositIntoBankAccount(AccountNumber $accountNumber, int $amount): void;
}
