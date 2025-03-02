<?php

declare(strict_types=1);

namespace App\Application\Query\GetBankAccounts;

use App\Application\Query\GetBankAccounts\GetBankAccounts;
use App\Application\Query\QueryHandler;
use App\Domain\Repository\BankAccountProvider;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class Handler implements QueryHandler
{
    public function __construct(
        private readonly BankAccountProvider $bankAccountProvider,
    ) {
    }

    public function __invoke(GetBankAccounts $getBankAccounts): array
    {
        return $this->bankAccountProvider->listAllBankAccounts();
    }
}
