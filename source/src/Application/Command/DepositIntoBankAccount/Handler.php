<?php

declare(strict_types=1);

namespace App\Application\Command\DepositIntoBankAccount;

use App\Application\Command\CommandHandler;
use App\Domain\Repository\BankAccountDepositor;
use App\Domain\ValueObject\AccountNumber;

final class Handler implements CommandHandler
{
    public function __construct(
        private readonly BankAccountDepositor $bankAccountDepositor,
    ) {
    }

    public function __invoke(DepositIntoBankAccount $depositIntoBankAccount): void
    {
        $accountNumber = AccountNumber::fromString($depositIntoBankAccount->getAccountNumber());

        $this->bankAccountDepositor->depositIntoBankAccount(
            $accountNumber,
            $depositIntoBankAccount->getAmount(),
        );
    }
}
