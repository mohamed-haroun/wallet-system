<?php

namespace App\Repositories;

use App\Models\Wallet;
use App\Models\WalletLedger;
use App\Models\WalletType;
use Illuminate\Support\Facades\DB;

class WalletRepository
{
    public function find(string $walletId): Wallet
    {
        return Wallet::findOrFail($walletId);
    }

    public function findByTag(string $walletTag): Wallet
    {
        return Wallet::where('wallet_tag', $walletTag)->firstOrFail();
    }

    public function getByUser(string $userId)
    {
        return Wallet::where('user_id', $userId)->get();
    }

    /**
     * Get a user's wallet by specific type
     *
     * @param string $userId
     * @param int $walletTypeId
     * @return Wallet|null
     */
    public function getUserWalletByType(string $userId, int $walletTypeId): ?Wallet
    {
        return Wallet::where('user_id', $userId)
            ->where('wallet_type_id', $walletTypeId)
            ->first();
    }

    public function create(array $data): Wallet
    {
        return Wallet::create($data);
    }

    public function updateStatus(string $walletId, bool $isActive): void
    {
        Wallet::where('id', $walletId)->update(['is_active' => $isActive]);
    }

    public function createLedgerEntry(array $data): WalletLedger
    {
        return WalletLedger::create($data);
    }

    /**
     * Get wallet with its type information
     *
     * @param string $walletId
     * @return Wallet
     */
    public function getWalletWithType(string $walletId): Wallet
    {
        return Wallet::with('walletType')->findOrFail($walletId);
    }

    /**
     * Update wallet balances
     *
     * @param string $walletId
     * @param array $balances
     * @return void
     */
    public function updateBalances(string $walletId, array $balances): void
    {
        Wallet::where('id', $walletId)->update($balances);
    }
}
