<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Infrastructure\Repository\TransactionMysqlRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/transaction', name: 'cqrs_transaction')]
class TransactionController extends AbstractController
{
    public function __construct(
        private readonly TransactionMysqlRepository $transactionRepository,
    ) {
    }

    #[Route('/list/account/{bankAccountId}', name: '_list')]
    public function transactionList(int $bankAccountId, Request $request): Response
    {
        $modifier = trim($request->getPayload()->getString('modifier'));
        $query = trim($request->getPayload()->getString('query'));

        $transactions = $this->transactionRepository->listTransactionsByBankAccountId(
            $bankAccountId,
            $modifier,
            $query,
        );

        return $this->render('transaction/list.html.twig', [
            'bankAccountId' => $bankAccountId,
            'transactions' => $transactions,
            'modifier' => $modifier,
            'query' => $query,
        ]);
    }
}
