<?php

namespace App\Contracts;

interface TransactionServiceInterface
{
    public function createTransaction(array $data): array;
    public function processTransaction(string $transactionId): array;
    public function reverseTransaction(string $transactionId, ?string $adminId = null): array;
    public function getTransaction(string $transactionId): array;
    public function getWalletTransactions(string $walletId, array $filters = []): array;
}
