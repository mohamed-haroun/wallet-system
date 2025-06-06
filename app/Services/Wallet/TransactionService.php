<?php

namespace App\Services\Wallet;

use App\Contracts\TransactionServiceInterface;
use App\Repositories\TransactionRepository;
use App\Repositories\WalletRepository;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\TransactionStatus;
use App\Exceptions\WalletException;
use App\Exceptions\InsufficientFundsException;
use App\Exceptions\TransactionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionService implements TransactionServiceInterface
{
    protected $transactionRepository;
    protected $walletRepository;

    public function __construct(
        TransactionRepository $transactionRepository,
        WalletRepository $walletRepository
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->walletRepository = $walletRepository;
    }

    public function getTransaction(string $transactionId): array
    {
        $transaction = $this->transactionRepository->find($transactionId);

        return [
            'id' => $transaction->id,
            'wallet_id' => $transaction->wallet_id,
            'wallet_tag' => $transaction->wallet->wallet_tag,
            'type' => $transaction->transactionType->code,
            'type_name' => $transaction->transactionType->name,
            'status' => $transaction->status->name,
            'status_color' => $transaction->status->color,
            'amount' => $transaction->amount,
            'fee' => $transaction->fee,
            'net_amount' => $transaction->net_amount,
            'reference' => $transaction->reference,
            'external_reference' => $transaction->external_reference,
            'narration' => $transaction->narration,
            'meta' => $transaction->meta,
            'ip_address' => $transaction->ip_address,
            'device_id' => $transaction->device_id,
            'location' => $transaction->location,
            'reversed_at' => $transaction->reversed_at,
            'reversed_by' => $transaction->reversed_by,
            'created_at' => $transaction->created_at,
            'updated_at' => $transaction->updated_at,
        ];
    }


    public function createTransaction(array $data): array
    {
        $wallet = $this->walletRepository->find($data['wallet_id']);
        $transactionType = TransactionType::where('code', $data['transaction_type'])->firstOrFail();

        // Validate transaction based on type
        $this->validateTransaction($wallet, $transactionType, $data['amount']);

        // Calculate net amount (amount - fee)
        $fee = $data['fee'] ?? 0;
        $netAmount = $transactionType->flow === 'in'
            ? $data['amount'] - $fee
            : $data['amount'] + $fee;

        $transaction = $this->transactionRepository->create([
            'wallet_id' => $wallet->id,
            'transaction_type_id' => $transactionType->id,
            'status_id' => TransactionStatus::where('name', 'pending')->first()->id,
            'amount' => $data['amount'],
            'fee' => $fee,
            'net_amount' => $netAmount,
            'reference' => $data['reference'] ?? Str::uuid(),
            'external_reference' => $data['external_reference'] ?? null,
            'narration' => $data['narration'] ?? null,
            'meta' => $data['meta'] ?? null,
            'ip_address' => $data['ip_address'] ?? request()->ip(),
        ]);

        return $transaction->toArray();
    }

    public function processTransaction(string $transactionId): array
    {
        $transaction = $this->transactionRepository->find($transactionId);

        if ($transaction->status->name !== 'pending') {
            throw new TransactionException('Transaction is not in pending state');
        }

        $wallet = $transaction->wallet;
        $transactionType = $transaction->transactionType;

        try {
            DB::beginTransaction();

            // Update wallet balances
            $newBalance = $this->updateWalletBalance($wallet, $transaction);

            // Update transaction status
            $this->transactionRepository->updateStatus(
                $transaction->id,
                TransactionStatus::where('name', 'completed')->first()->id
            );

            // Record ledger entry
            $this->walletRepository->createLedgerEntry([
                'wallet_id' => $wallet->id,
                'balance_before' => $transactionType->flow === 'in'
                    ? $wallet->available_balance - $transaction->net_amount
                    : $wallet->available_balance + $transaction->net_amount,
                'balance_after' => $newBalance,
                'amount' => $transaction->net_amount,
                'reference' => $transaction->reference,
            ]);

            DB::commit();

            return $transaction->fresh()->toArray();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Reverse a completed transaction
     *
     * @param string $transactionId
     * @param string|null $adminId
     * @return array
     * @throws TransactionException
     */
    public function reverseTransaction(string $transactionId, string $adminId = null): array
    {
        return DB::transaction(function () use ($transactionId, $adminId) {
            // 1. Get and lock the transaction
            $transaction = $this->transactionRepository->find($transactionId);

            // 2. Validate transaction can be reversed
            if (!$transaction->status->isCompleted()) {
                throw new TransactionException('Only completed transactions can be reversed');
            }

            if ($transaction->reversed_at) {
                throw new TransactionException('Transaction is already reversed');
            }

            $wallet = $transaction->wallet;
            $transactionType = $transaction->transactionType;

            // 3. Create reversal transaction
            $reversalTransaction = $this->transactionRepository->create([
                'wallet_id' => $wallet->id,
                'transaction_type_id' => $transactionType->id,
                'status_id' => TransactionStatus::where('name', 'pending')->first()->id,
                'amount' => $transaction->amount,
                'fee' => $transaction->fee,
                'net_amount' => $transaction->net_amount,
                'reference' => 'REV-' . $transaction->reference,
                'external_reference' => $transaction->external_reference,
                'narration' => 'Reversal of ' . $transaction->narration,
                'meta' => array_merge($transaction->meta ?? [], [
                    'original_transaction_id' => $transaction->id,
                    'reversal_reason' => 'Admin initiated'
                ]),
                'ip_address' => request()->ip(),
            ]);

            // 4. Process the reversal (updates balances)
            $this->processTransaction($reversalTransaction->id);

            // 5. Mark original transaction as reversed
            $this->transactionRepository->reverseTransaction(
                $transaction->id,
                $adminId,
                'Admin reversal'
            );

            // 6. Record ledger entries for both transactions
            $this->walletRepository->createLedgerEntry([
                'wallet_id' => $wallet->id,
                'balance_before' => $wallet->available_balance + $transaction->net_amount,
                'balance_after' => $wallet->available_balance,
                'amount' => -$transaction->net_amount,
                'reference' => $reversalTransaction->reference,
            ]);

            return [
                'original_transaction' => $this->getTransaction($transaction->id),
                'reversal_transaction' => $this->getTransaction($reversalTransaction->id),
                'new_balance' => $this->walletRepository->find($wallet->id)->available_balance
            ];
        });
    }

    protected function validateTransaction($wallet, $transactionType, $amount): void
    {
        if ($transactionType->flow === 'out') {
            if (!$wallet->walletType->allow_negative && $wallet->available_balance < $amount) {
                throw new InsufficientFundsException('Insufficient funds in wallet');
            }
        }

        if (!$wallet->is_active) {
            throw new WalletException('Wallet is inactive');
        }

        if (!$wallet->walletType->is_active) {
            throw new WalletException('Wallet type is inactive');
        }
    }

    public function getWalletTransactions(string $walletId, array $filters = []): array
    {
        $transactions = $this->transactionRepository->getWalletTransactions($walletId, $filters);

        return $transactions->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'type' => $transaction->transactionType->code,
                'type_name' => $transaction->transactionType->name,
                'status' => $transaction->status->name,
                'status_color' => $transaction->status->color,
                'amount' => $transaction->amount,
                'fee' => $transaction->fee,
                'net_amount' => $transaction->net_amount,
                'reference' => $transaction->reference,
                'narration' => $transaction->narration,
                'created_at' => $transaction->created_at,
                'is_reversed' => !is_null($transaction->reversed_at),
            ];
        })->toArray();
    }

    protected function updateWalletBalance($wallet, $transaction): float
    {
        $transactionType = $transaction->transactionType;
        $amount = $transaction->net_amount;

        if ($transactionType->flow === 'in') {
            $wallet->available_balance += $amount;
            $wallet->total_earned += $amount;
        } else {
            $wallet->available_balance -= $amount;
            $wallet->total_spent += $amount;
        }

        $wallet->save();

        return $wallet->available_balance;
    }
}
