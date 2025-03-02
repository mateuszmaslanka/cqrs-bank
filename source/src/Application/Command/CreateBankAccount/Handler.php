<?php

declare(strict_types=1);

namespace App\Application\Command\CreateBankAccount;

use App\Application\Command\CommandHandler;
use App\Application\Command\CreateBankAccount\CreateBankAccount;
use App\Domain\Generator\AccountNumberGenerator;
use App\Domain\Repository\BankAccountCreator;

final class Handler implements CommandHandler
{
    public function __construct(
        private readonly AccountNumberGenerator $accountNumberGenerator, 
        private readonly BankAccountCreator $bankAccountCreator,
    ) {
    }

    public function __invoke(CreateBankAccount $createBankAccount): void
    {
        $accountNumber = $this->accountNumberGenerator->generate();

        $this->bankAccountCreator->createNewBankAccount($createBankAccount->getId(), $accountNumber);
    }
}
