<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Model\BankAccount;
use App\Domain\Repository\BankAccountCreator;
use App\Domain\Repository\BankAccountDepositor;
use App\Domain\Repository\BankAccountPayer;
use App\Domain\Repository\BankAccountProvider;
use App\Domain\ValueObject\AccountBalance;
use App\Domain\ValueObject\AccountNumber;
use Doctrine\DBAL\Connection;

class BankAccountMysqlRepository implements BankAccountProvider, BankAccountCreator, BankAccountDepositor, BankAccountPayer
{
    public function __construct(
        private readonly Connection $connection,
        private readonly TransactionMysqlRepository $tansactionRepository,
    ) {
        $this->createDatabaseIfNotExists();
    }

    private function createDatabaseIfNotExists(): void
    {
        $this->connection->executeStatement('CREATE DATABASE IF NOT EXISTS cqrs');
        $this->connection->executeStatement('USE cqrs');
        $this->connection->executeStatement('
            CREATE TABLE IF NOT EXISTS bank_accounts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                account_number VARCHAR(255) NOT NULL UNIQUE,
                account_balance INT NOT NULL DEFAULT 0
            )
        ');
    }

    private function dataToBankAccount(array $data): BankAccount
    {
        $className = BankAccount::class;
        $propCount = count($data);
        $serializedProps = '';

        $idProp = "\0" . (string)$className . "\0" . 'id';
        $idSerailized = 's:' . mb_strlen($idProp) . ':"' . $idProp . '";' . serialize((int) $data['id']);

        $accountNumber = AccountNumber::fromString($data['account_number']);
        $accountNumberProp = "\0" . (string)$className . "\0" . 'accountNumber';
        $accountNumberSerialized = 's:' . mb_strlen($accountNumberProp) . ':"' . $accountNumberProp . '";' . serialize($accountNumber);

        $accountBalance = AccountBalance::fromInt((int) $data['account_balance']);
        $accountBalanceProp = "\0" . (string)$className . "\0" . 'accountBalance';
        $accountBalanceSerialized = 's:' . mb_strlen($accountBalanceProp) . ':"' . $accountBalanceProp . '";' . serialize($accountBalance);

        $serializedProps .= $accountBalanceSerialized . $idSerailized . $accountNumberSerialized;
        $serialized = 'O:' . mb_strlen((string)$className) . ':"' . $className . '":' . $propCount . ':{' . $serializedProps . '}';

        return unserialize($serialized);
    }

    public function listAllBankAccounts(): array
    {
        $list = [];

        foreach ($this->connection->fetchAllAssociative('SELECT * FROM bank_accounts') as $data) {
            $list[] = $this->dataToBankAccount($data);
        }

        return $list;
    }

    public function findOneBankAccountById(int $id): BankAccount
    {
        $data = $this->connection->fetchAssociative(
            'SELECT id, account_number, account_balance FROM bank_accounts WHERE id = :id',
            [
                'id' => $id,
            ],
        );

        if (!$data) {
            throw new \RuntimeException('Account not found');
        }

        return $this->dataToBankAccount($data);
    }

    public function findOneBankAccountByAccountNumber(AccountNumber $accountNumber): BankAccount
    {
        $data = $this->connection->fetchAssociative(
            'SELECT id, account_number, account_balance FROM bank_accounts WHERE account_number = :account_number',
            [
                'account_number' => (string) $accountNumber,
            ],
        );

        if (!$data) {
            throw new \RuntimeException('Account not found');
        }


        return $this->dataToBankAccount($data);
    }

    public function createNewBankAccount(int $id, AccountNumber $accountNumber): void
    {
        $this->connection->insert(
            'bank_accounts',
            [
                'id' => $id,
                'account_number' => (string) $accountNumber,
                'account_balance' => 0,
            ],
        );
    }

    public function depositIntoBankAccount(AccountNumber $accountNumber, int $amount): void
    {
        $bankAccount = $this->findOneBankAccountByAccountNumber($accountNumber);
        $bankAccount->deposite($amount);

        $this->connection->update(
            'bank_accounts',
            [
                'account_balance' => $bankAccount->getAccountBalanceValue(),
            ],
            [
                'account_number' => (string) $accountNumber,
            ],
        );

        $this->tansactionRepository->addTransaction(
            $bankAccount->getId(),
            $amount,
        );
    }

    public function payFromBankAccount(AccountNumber $accountNumber, int $amount): void
    {
        $bankAccount = $this->findOneBankAccountByAccountNumber($accountNumber);
        $bankAccount->pay($amount);

        $this->connection->update(
            'bank_accounts',
            [
                'account_balance' => $bankAccount->getAccountBalanceValue(),
            ],
            [
                'account_number' => (string) $accountNumber,
            ],
        );

        $this->tansactionRepository->addTransaction(
            $bankAccount->getId(),
            -$amount,
        );
    }
}
