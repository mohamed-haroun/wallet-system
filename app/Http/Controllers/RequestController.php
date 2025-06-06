<?php

namespace App\Http\Controllers;

use App\Services\Wallet\RequestService;
use App\Exceptions\WalletException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class RequestController extends Controller
{
    protected $requestService;

    public function __construct(RequestService $requestService)
    {
        $this->requestService = $requestService;
    }

    /**
     * Create a new request
     */
    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'request_type' => 'required|string|exists:request_types,code',
            'user_id' => 'required|exists:users,id',
            'wallet_id' => 'required|exists:wallets,id',
            'amount' => 'required|numeric|min:0',
            'details' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $requestData = $validator->validated();

            if ($request->hasFile('attachments')) {
                $requestData['attachments'] = $this->handleAttachments($request->file('attachments'));
            }

            $newRequest = $this->requestService->createRequest($requestData);
            return response()->json($newRequest, 201);
        } catch (WalletException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Process a request (approve/reject/hold)
     */
    public function process(Request $request, string $requestId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'admin_id' => 'required|exists:users,id',
            'action' => 'required|in:approve,reject,hold',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $validated = $validator->validated();
            $processedRequest = $this->requestService->processRequest(
                $requestId,
                $validated['admin_id'],
                $validated['action'],
                $validated['notes'] ?? null
            );
            return response()->json($processedRequest);
        } catch (WalletException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Get request details
     */
    public function show(string $requestId): JsonResponse
    {
        try {
            $request = $this->requestService->getRequest($requestId);
            return response()->json($request);
        } catch (WalletException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    /**
     * Get user's requests
     */
    public function userRequests(Request $request, string $userId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'nullable|string',
            'request_type' => 'nullable|string|exists:request_types,code',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $filters = $validator->validated();
            $requests = $this->requestService->getUserRequests($userId, $filters);
            return response()->json($requests);
        } catch (WalletException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Handle file attachments
     */
    protected function handleAttachments(array $files): array
    {
        $attachments = [];

        foreach ($files as $file) {
            $path = $file->store('requests/attachments');
            $attachments[] = [
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
            ];
        }

        return $attachments;
    }
}
