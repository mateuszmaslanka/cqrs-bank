<?php

declare(strict_types=1);

namespace App\Application\Command\DepositIntoBankAccount;

use App\Application\Command\Command;

final class DepositIntoBankAccount implements Command
{
    public function __construct(
        private string $accountNumber,
        private int $amount,
    ) {
    }

    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }
}
