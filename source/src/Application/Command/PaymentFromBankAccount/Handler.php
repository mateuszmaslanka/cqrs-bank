<?php

declare(strict_types=1);

namespace App\Application\Command\PaymentFromBankAccount;

use App\Application\Command\CommandHandler;
use App\Domain\Repository\BankAccountPayer;
use App\Domain\ValueObject\AccountNumber;

final class Handler implements CommandHandler
{
    public function __construct(
        private readonly BankAccountPayer $bankAccountPayer,
    ) {
    }

    public function __invoke(PaymentFromBankAccount $paymentFromBankAccount): void
    {
        $accountNumber = AccountNumber::fromString($paymentFromBankAccount->getAccountNumber());

        $this->bankAccountPayer->payFromBankAccount(
            $accountNumber,
            $paymentFromBankAccount->getAmount(),
        );
    }
}
