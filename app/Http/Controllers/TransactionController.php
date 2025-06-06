<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\Wallet\TransactionService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Display a specific transaction.
     */
    public function show($id)
    {
        try {
            $transaction = $this->transactionService->getTransaction($id);
            return response()->json(['data' => $transaction]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Transaction not found.'], 404);
        }
    }

    /**
     * Create a new transaction (e.g., deposit, withdraw).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'wallet_id' => 'required|uuid',
            'transaction_type' => 'required|string', // 'deposit' or 'withdraw', etc.
            'amount' => 'required|numeric|min:0.01',
            'fee' => 'nullable|numeric|min:0',
            'reference' => 'nullable|string',
            'external_reference' => 'nullable|string',
            'narration' => 'nullable|string',
            'meta' => 'nullable|array',
            'ip_address' => 'nullable|ip',
        ]);

        try {
            $transaction = $this->transactionService->createTransaction($data);
            return response()->json(['data' => $transaction], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Transaction could not be created.', 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Process a pending transaction.
     */
    public function process($id)
    {
        try {
            $transaction = $this->transactionService->processTransaction($id);
            return response()->json(['data' => $transaction]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Processing failed', 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Reverse a completed transaction.
     */
    public function reverse(Request $request, $id)
    {
        $request->validate([
            'admin_id' => 'nullable|uuid',
        ]);

        try {
            $reversal = $this->transactionService->reverseTransaction($id, $request->admin_id);
            return response()->json(['data' => $reversal]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Reversal failed', 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * List all transactions for a wallet.
     */
    public function walletTransactions(Request $request, $walletId)
    {
        $filters = $request->only(['type', 'status', 'from', 'to']);

        try {
            $transactions = $this->transactionService->getWalletTransactions($walletId, $filters);
            return response()->json(['data' => $transactions]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to fetch transactions', 'message' => $e->getMessage()], 400);
        }
    }
}
