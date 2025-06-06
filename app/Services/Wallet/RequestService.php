<?php

namespace App\Services\Wallet;

use App\Contracts\RequestServiceInterface;
use App\Repositories\RequestRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\WalletRepository;
use App\Models\Request;
use App\Models\RequestType;
use App\Models\TransactionStatus;
use App\Exceptions\WalletException;
use App\Models\TransactionType;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Events\RequestCreated;
use App\Events\RequestStatusUpdated;

class RequestService implements RequestServiceInterface
{
    protected $requestRepository;
    protected $transactionRepository;
    protected $walletRepository;

    public function __construct(
        RequestRepository $requestRepository,
        TransactionRepository $transactionRepository,
        WalletRepository $walletRepository
    ) {
        $this->requestRepository = $requestRepository;
        $this->transactionRepository = $transactionRepository;
        $this->walletRepository = $walletRepository;
    }

    public function createRequest(array $data): array
    {
        $requestType = RequestType::where('code', $data['request_type'])->firstOrFail();
        $wallet = $this->walletRepository->find($data['wallet_id']);

        if ($wallet->user_id !== $data['user_id']) {
            throw new WalletException('Wallet does not belong to user');
        }

        $request = $this->requestRepository->create([
            'request_type_id' => $requestType->id,
            'user_id' => $data['user_id'],
            'wallet_id' => $data['wallet_id'],
            'amount' => $data['amount'],
            'details' => $data['details'] ?? null,
            'status_id' => TransactionStatus::where('name', 'pending')->first()->id,
            'attachments' => $data['attachments'] ?? null,
        ]);

        // Dispatch request created event
        event(new RequestCreated($request));

        return $request->toArray();
    }

    public function processRequest(string $requestId, string $adminId, string $action, ?string $notes = null): array
    {
        return DB::transaction(function () use ($requestId, $adminId, $action, $notes) {
            $request = $this->requestRepository->find($requestId);

            if ($request->status->name !== 'pending') {
                throw new WalletException('Request is not in pending state');
            }

            // Update request status based on action
            $newStatus = $this->determineStatusFromAction($action);
            $this->requestRepository->updateStatus($request->id, $newStatus->id, $adminId, $notes);

            // If approved, create and process transaction
            if ($action === 'approve') {
                $transactionType = $this->getTransactionTypeForRequest($request->requestType);

                $transaction = $this->transactionRepository->create([
                    'wallet_id' => $request->wallet_id,
                    'transaction_type_id' => $transactionType->id,
                    'status_id' => TransactionStatus::where('name', 'pending')->first()->id,
                    'amount' => $request->amount,
                    'narration' => $request->details,
                    'reference' => 'REQ-' . Str::upper(Str::random(10)),
                    'meta' => ['request_id' => $request->id],
                ]);

                $processedTransaction = $this->transactionRepository->processTransaction($transaction->id);

                $request->update(['transaction_id' => $transaction->id]);
            }

            // Record approval action
            $this->requestRepository->createApproval($request->id, $adminId, $action, $notes);

            // Dispatch status updated event
            event(new RequestStatusUpdated($request, $adminId, $action));

            return $request->fresh()->load(['status', 'requestType', 'transaction'])->toArray();
        });
    }

    public function getRequest(string $requestId): array
    {
        $request = $this->requestRepository->find($requestId);

        return [
            'id' => $request->id,
            'request_type' => $request->requestType->name,
            'request_type_code' => $request->requestType->code,
            'user_id' => $request->user_id,
            'wallet_id' => $request->wallet_id,
            'wallet_tag' => $request->wallet->wallet_tag,
            'amount' => $request->amount,
            'details' => $request->details,
            'status' => $request->status->name,
            'status_color' => $request->status->color,
            'processed_by' => $request->processed_by,
            'processed_at' => $request->processed_at,
            'admin_notes' => $request->admin_notes,
            'rejection_reason' => $request->rejection_reason,
            'attachments' => $request->attachments,
            'created_at' => $request->created_at,
            'updated_at' => $request->updated_at,
            'transaction_id' => $request->transaction_id,
            'approvals' => $request->approvals->map(function ($approval) {
                return [
                    'admin_id' => $approval->admin_id,
                    'action' => $approval->action,
                    'notes' => $approval->notes,
                    'created_at' => $approval->created_at
                ];
            })->toArray()
        ];
    }

    public function getUserRequests(string $userId, array $filters = []): array
    {
        $requests = $this->requestRepository->getUserRequests($userId, $filters);

        return $requests->map(function ($request) {
            return [
                'id' => $request->id,
                'request_type' => $request->requestType->name,
                'amount' => $request->amount,
                'status' => $request->status->name,
                'status_color' => $request->status->color,
                'created_at' => $request->created_at,
                'processed_at' => $request->processed_at,
                'is_approved' => $request->status->name === 'completed',
                'is_rejected' => $request->status->name === 'failed',
                'transaction_id' => $request->transaction_id
            ];
        })->toArray();
    }

    protected function determineStatusFromAction(string $action)
    {
        return match ($action) {
            'approve' => TransactionStatus::where('name', 'completed')->first(),
            'reject' => TransactionStatus::where('name', 'failed')->first(),
            'hold' => TransactionStatus::where('name', 'pending')->first(),
            default => throw new WalletException('Invalid action'),
        };
    }

    protected function getTransactionTypeForRequest(RequestType $requestType)
    {
        return match ($requestType->code) {
            'withdrawal' => TransactionType::where('code', 'WD')->first(),
            'topup' => TransactionType::where('code', 'DP')->first(),
            'transfer' => TransactionType::where('code', 'TF')->first(),
            default => throw new WalletException('Unsupported request type'),
        };
    }

    public function getAllRequests(array $filters = []): array
    {
        $requests = $this->requestRepository->getAllRequests($filters);

        return $requests->map(function ($request) {
            return $this->getRequest($request->id);
        })->toArray();
    }
}
