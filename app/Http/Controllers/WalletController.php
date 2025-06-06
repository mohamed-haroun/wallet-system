<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Wallet\WalletService;
use Illuminate\Http\JsonResponse;

class WalletController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|string|exists:users,id',
            'wallet_type_id' => 'required|integer|exists:wallet_types,id',
            'currency_code' => 'sometimes|string|max:3'
        ]);

        $wallet = $this->walletService->createWallet(
            $validated['user_id'],
            $validated['wallet_type_id'],
            $validated['currency_code'] ?? 'EGP'
        );

        return response()->json($wallet, 201);
    }

    public function balance($walletId): JsonResponse
    {
        $balance = $this->walletService->getWalletBalance($walletId);
        return response()->json($balance);
    }

    public function byTag($walletTag): JsonResponse
    {
        $wallet = $this->walletService->getWalletByTag($walletTag);
        return response()->json($wallet);
    }

    public function byUser($userId): JsonResponse
    {
        $wallets = $this->walletService->getWalletsByUser($userId);
        return response()->json($wallets);
    }

    public function updateStatus(Request $request, $walletId): JsonResponse
    {
        $validated = $request->validate([
            'is_active' => 'required|boolean'
        ]);

        $this->walletService->updateWalletStatus($walletId, $validated['is_active']);

        return response()->json(['message' => 'Wallet status updated successfully']);
    }

    public function recordLedger(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'wallet_id' => 'required|string|exists:wallets,id',
            'amount' => 'required|numeric',
            'type' => 'required|string|in:credit,debit',
            'description' => 'nullable|string',
            'reference_id' => 'nullable|string',
        ]);

        $this->walletService->recordLedgerEntry($validated);

        return response()->json(['message' => 'Ledger entry recorded']);
    }
}
