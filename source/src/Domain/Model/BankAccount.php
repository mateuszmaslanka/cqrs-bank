<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\ValueObject\AccountNumber;

class BankAccount
{
    public function __construct(
        private int $id,
        private AccountNumber $accountNumber,
    ){ 
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAccountNumber(): string
    {
        return $this->accountNumber->format();
    }
}
