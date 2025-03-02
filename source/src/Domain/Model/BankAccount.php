<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\ValueObject\AccountBalance;
use App\Domain\ValueObject\AccountNumber;

class BankAccount
{
    private AccountBalance $accountBalance;

    public function __construct(
        private int $id,
        private AccountNumber $accountNumber,
    ) {
        $this->accountBalance = new AccountBalance();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function deposite(int $amount): void
    {
        $this->accountBalance->add($amount);
    }

    public function getAccountNumber(): AccountNumber
    {
        return $this->accountNumber;
    }

    public function getAccountBalance(): string
    {
        return $this->accountBalance->format();
    }
}
