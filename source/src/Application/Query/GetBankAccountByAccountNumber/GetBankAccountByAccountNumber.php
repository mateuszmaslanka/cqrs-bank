<?php

declare(strict_types=1);

namespace App\Application\Query\GetBankAccountByAccountNumber;

use App\Application\Query\Query;

class GetBankAccountByAccountNumber implements Query
{
    public function __construct(
        private readonly string $accountNumber,
    ) {
    }

    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }
}
