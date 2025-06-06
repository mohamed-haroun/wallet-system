<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Models\TransactionStatus;
use Illuminate\Support\Facades\DB;

class TransactionRepository
{
    public function find(string $transactionId): Transaction
    {
        return Transaction::with(['wallet', 'transactionType', 'status'])->findOrFail($transactionId);
    }

    public function findByReference(string $reference): ?Transaction
    {
        return Transaction::where('reference', $reference)->first();
    }

    public function create(array $data): Transaction
    {
        return Transaction::create($data);
    }

    public function updateStatus(string $transactionId, int $statusId): void
    {
        Transaction::where('id', $transactionId)->update(['status_id' => $statusId]);
    }

    public function reverseTransaction(string $transactionId, string $reversedBy, string $reversalReason = null): Transaction
    {
        return DB::transaction(function () use ($transactionId, $reversedBy, $reversalReason) {
            $transaction = Transaction::lockForUpdate()->findOrFail($transactionId);

            $transaction->update([
                'status_id' => TransactionStatus::where('name', 'reversed')->first()->id,
                'reversed_at' => now(),
                'reversed_by' => $reversedBy,
                'meta' => array_merge($transaction->meta ?? [], ['reversal_reason' => $reversalReason])
            ]);

            return $transaction;
        });
    }

    public function getWalletTransactions(string $walletId, array $filters = [])
    {
        $query = Transaction::where('wallet_id', $walletId)
            ->with(['transactionType', 'status'])
            ->orderBy('created_at', 'desc');

        if (isset($filters['type'])) {
            $query->whereHas('transactionType', function ($q) use ($filters) {
                $q->where('code', $filters['type']);
            });
        }

        if (isset($filters['status'])) {
            $query->whereHas('status', function ($q) use ($filters) {
                $q->where('name', $filters['status']);
            });
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return isset($filters['paginate'])
            ? $query->paginate($filters['per_page'] ?? 15)
            : $query->get();
    }

    /**
     * Process a transaction by updating wallet balances and status
     *
     * @param string $transactionId
     * @return Transaction
     * @throws \Exception
     */
    public function processTransaction(string $transactionId): Transaction
    {
        return DB::transaction(function () use ($transactionId) {
            $transaction = Transaction::lockForUpdate()->with(['wallet', 'transactionType', 'status'])->findOrFail($transactionId);

            if ($transaction->status->name !== 'pending') {
                throw new \Exception('Transaction is not in pending state');
            }

            $wallet = $transaction->wallet;
            $transactionType = $transaction->transactionType;
            $amount = $transaction->net_amount;

            // Update wallet balance based on transaction flow
            if ($transactionType->flow === 'in') {
                $wallet->available_balance += $amount;
                $wallet->total_earned += $transaction->amount;
            } else {
                $wallet->available_balance -= $amount;
                $wallet->total_spent += $transaction->amount;
            }

            $wallet->save();

            // Update transaction status to completed
            $completedStatus = TransactionStatus::where('name', 'completed')->first();
            $transaction->status_id = $completedStatus->id;
            $transaction->save();

            return $transaction->refresh();
        });
    }
}
