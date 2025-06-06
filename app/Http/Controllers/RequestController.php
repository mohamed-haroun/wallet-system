<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Wallet\RequestService;

class RequestController extends Controller
{
    protected $requestService;

    public function __construct(RequestService $requestService)
    {
        $this->requestService = $requestService;
    }

    public function create(Request $request)
    {
        $data = $request->validate([
            'request_type' => 'required|string',
            'user_id' => 'required|string',
            'wallet_id' => 'required|string',
            'amount' => 'required|numeric',
            'details' => 'nullable|string',
            'attachments' => 'nullable|array',
        ]);

        $newRequest = $this->requestService->createRequest($data);

        return response()->json($newRequest);
    }

    public function process(Request $request, string $requestId)
    {
        $data = $request->validate([
            'admin_id' => 'required|string',
            'action' => 'required|in:approve,reject,hold',
            'notes' => 'nullable|string',
        ]);

        $processed = $this->requestService->processRequest(
            $requestId,
            $data['admin_id'],
            $data['action'],
            $data['notes'] ?? null
        );

        return response()->json($processed);
    }

    public function show(string $requestId)
    {
        $requestDetails = $this->requestService->getRequest($requestId);
        return response()->json($requestDetails);
    }

    public function listByUser(string $userId, Request $request)
    {
        $filters = $request->only(['status', 'type', 'date_from', 'date_to']);
        $requests = $this->requestService->getUserRequests($userId, $filters);

        return response()->json($requests);
    }
}
