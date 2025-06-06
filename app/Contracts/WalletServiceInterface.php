<?php

namespace App\Contracts;

interface WalletServiceInterface
{
    public function createWallet(string $userId, int $walletTypeId, string $currencyCode = 'EGP'): array;
    public function getWalletBalance(string $walletId): array;
    public function getWalletByTag(string $walletTag): array;
    public function getWalletsByUser(string $userId): array;
    public function updateWalletStatus(string $walletId, bool $isActive): void;
    public function recordLedgerEntry(array $data): void;
}
