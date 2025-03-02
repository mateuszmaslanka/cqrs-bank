<?php

declare(strict_types=1);

namespace App\Application\Query\GetBankAccountById;

use App\Application\Query\GetBankAccountById\GetBankAccountById;
use App\Application\Query\QueryHandler;
use App\Domain\Model\BankAccount;
use App\Domain\Repository\BankAccountProvider;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class Handler implements QueryHandler
{
    public function __construct(
        private readonly BankAccountProvider $bankAccountProvider,
    ) {
    }

    public function __invoke(GetBankAccountById $getBankAccount): BankAccount
    {
        return $this->bankAccountProvider->findOneBankAccountById($getBankAccount->getId());
    }
}
