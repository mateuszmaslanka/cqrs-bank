<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use Doctrine\DBAL\Connection;

class TransactionMysqlRepository
{
    public function __construct(
        private readonly Connection $connection
    ) {
        $this->createDatabaseIfNotExists();
    }

    private function createDatabaseIfNotExists(): void
    {
        $this->connection->executeStatement('CREATE DATABASE IF NOT EXISTS cqrs');
        $this->connection->executeStatement('USE cqrs');
        $this->connection->executeStatement('
            CREATE TABLE IF NOT EXISTS transactions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                bank_account_id INT NOT NULL,
                amount INT NOT NULL DEFAULT 0,
                title VARCHAR(500) NOT NULL DEFAULT "",
                FULLTEXT idx (title)
            ) ENGINE=InnoDB;
        ');

        // Fulltext index limitations:
        // - w transakcji tylko zacommitowane zmiany są widoczne
        // - kasowane rekordy są tylko oznaczane jako usunięte i trzeba okresowo odpalać OPTIMIZE
        // - QUERY EXAPNSION (twice search) - przydajesię raczej do krótkich fraz
    }

    public function addTransaction(int $bankAccountId, int $amount): void
    {
        foreach ($this->generateRandomTransactionTitles() as $title) {
            $this->connection->insert(
                'transactions',
                [
                    'bank_account_id' => $bankAccountId,
                    'amount' => $amount,
                    'title' => $title,
                ],
            );
        }
    }

    public function listTransactionsByBankAccountId(int $id, string $modifier, string $query): array
    {
        if ('' !== $modifier && '' !== $query) {
            $fulltextSearch = sprintf(' AND MATCH(title) AGAINST(:query %s)', $modifier);
        } else {
            $fulltextSearch = '';
        }

        return $this->connection->fetchAllAssociative(
            'SELECT * FROM transactions WHERE bank_account_id = :id' . $fulltextSearch,
            [
                'id' => $id,
                'query' => $query,
            ],
        );
    }

    private function generateRandomTransactionTitles(): array
    {
        return [
            '1. blik payment',
            '2. paid by blik',
            '3. by mastercard',
            '4. credit card payment',
            '5. debit card payment',
        ];
    }
}
