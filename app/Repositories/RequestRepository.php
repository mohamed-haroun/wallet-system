<?php

namespace App\Repositories;

use App\Models\Request;
use App\Models\RequestApproval;
use App\Models\TransactionStatus;

class RequestRepository
{
    public function find(string $requestId): Request
    {
        return Request::with([
            'requestType',
            'status',
            'wallet',
            'approvals',
            'transaction'
        ])->findOrFail($requestId);
    }

    public function getUserRequests(string $userId, array $filters = [])
    {
        $query = Request::where('user_id', $userId)
            ->with(['requestType', 'status', 'wallet'])
            ->orderBy('created_at', 'desc');

        if (isset($filters['type'])) {
            $query->whereHas('requestType', function ($q) use ($filters) {
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

    public function create(array $data): Request
    {
        return Request::create($data);
    }

    public function updateStatus(string $requestId, int $statusId, ?string $processedBy = null, ?string $notes = null): void
    {
        $updateData = ['status_id' => $statusId];

        if ($statusId === TransactionStatus::where('name', 'completed')->first()->id) {
            $updateData['processed_at'] = now();
            $updateData['processed_by'] = $processedBy;
        }

        if ($statusId === TransactionStatus::where('name', 'failed')->first()->id) {
            $updateData['rejection_reason'] = $notes;
        }

        Request::where('id', $requestId)->update($updateData);
    }

    public function createApproval(string $requestId, string $adminId, string $action, string $notes = null): RequestApproval
    {
        return RequestApproval::create([
            'request_id' => $requestId,
            'admin_id' => $adminId,
            'action' => $action,
            'notes' => $notes,
        ]);
    }
}
