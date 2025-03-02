<?php

declare(strict_types=1);

namespace App\Application\Query\GetBankAccountByAccountNumber;

use App\Application\Query\QueryHandler;
use App\Domain\Model\BankAccount;
use App\Domain\Repository\BankAccountProvider;
use App\Domain\ValueObject\AccountNumber;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class Handler implements QueryHandler
{
    public function __construct(
        private readonly BankAccountProvider $bankAccountProvider,
    ) {
    }

    public function __invoke(GetBankAccountByAccountNumber $getBankAccount): BankAccount
    {
        $accountNumber = AccountNumber::fromString($getBankAccount->getAccountNumber());

        return $this->bankAccountProvider->findOneBankAccountByAccountNumber($accountNumber);
    }
}
