<?php

declare(strict_types=1);

namespace App\Application\Query\GetBankAccountById;

use App\Application\Query\Query;

class GetBankAccountById implements Query
{
    public function __construct(
        private readonly int $id,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }
}
