<?php

declare(strict_types=1);

namespace App\Application\Command\CreateBankAccount;

use App\Application\Command\Command;

final class CreateBankAccount implements Command
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
