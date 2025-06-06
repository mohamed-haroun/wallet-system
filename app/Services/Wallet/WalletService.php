<?php

namespace App\Services\Wallet;

use App\Contracts\WalletServiceInterface;
use App\Repositories\WalletRepository;
use App\Models\Wallet;
use App\Models\WalletType;
use App\Exceptions\WalletException;
use Illuminate\Support\Str;

class WalletService implements WalletServiceInterface
{
    protected $walletRepository;

    public function __construct(WalletRepository $walletRepository)
    {
        $this->walletRepository = $walletRepository;
    }

    public function createWallet(string $userId, int $walletTypeId, string $currencyCode = 'EGP'): array
    {
        $walletType = WalletType::findOrFail($walletTypeId);

        $walletTag = 'WALLET-' . Str::upper(Str::random(4)) . '-' . Str::upper(Str::random(4));

        $wallet = $this->walletRepository->create([
            'user_id' => $userId,
            'wallet_type_id' => $walletTypeId,
            'currency_code' => $currencyCode,
            'wallet_tag' => $walletTag,
        ]);

        return $wallet->toArray();
    }

    public function getWalletBalance(string $walletId): array
    {
        $wallet = $this->walletRepository->find($walletId);

        return [
            'available_balance' => $wallet->available_balance,
            'pending_balance' => $wallet->pending_balance,
            'total_earned' => $wallet->total_earned,
            'total_spent' => $wallet->total_spent,
            'currency_code' => $wallet->currency_code,
        ];
    }

    public function getWalletByTag(string $walletTag): array
    {
        $wallet = $this->walletRepository->findByTag($walletTag);
        return $wallet->toArray();
    }

    public function getWalletsByUser(string $userId): array
    {
        $wallets = $this->walletRepository->getByUser($userId);
        return $wallets->toArray();
    }

    public function updateWalletStatus(string $walletId, bool $isActive): void
    {
        $this->walletRepository->updateStatus($walletId, $isActive);
    }

    public function recordLedgerEntry(array $data): void
    {
        $this->walletRepository->createLedgerEntry($data);
    }
}
