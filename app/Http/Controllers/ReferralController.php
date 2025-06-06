<?php

namespace App\Http\Controllers;

use App\Services\Wallet\ReferralService;
use App\Exceptions\WalletException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ReferralController extends Controller
{
    protected $referralService;

    public function __construct(ReferralService $referralService)
    {
        $this->referralService = $referralService;
    }

    /**
     * Get referral program details
     *
     * @param string $programId
     * @return JsonResponse
     */
    public function getProgram($programId): JsonResponse
    {
        try {
            $program = $this->referralService->getReferralProgram($programId);
            return response()->json($program);
        } catch (WalletException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    /**
     * Get user's referrals
     *
     * @param Request $request
     * @param string $userId
     * @return JsonResponse
     */
    public function getUserReferrals(Request $request, string $userId): JsonResponse
    {
        try {
            $referrals = $this->referralService->getUserReferrals($userId);
            return response()->json($referrals);
        } catch (WalletException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Create a new referral program
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createProgram(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:referral_programs,code',
            'referrer_reward' => 'required|numeric|min:0',
            'referee_reward' => 'required|numeric|min:0',
            'reward_type' => 'nullable|in:fixed,percentage',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $program = $this->referralService->createReferralProgram($validator->validated());
            return response()->json($program, 201);
        } catch (WalletException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Process a new referral
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function processReferral(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'referral_code' => 'required|string|exists:referral_programs,code',
            'referrer_id' => 'required|exists:users,id',
            'referee_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $validated = $validator->validated();
            $referral = $this->referralService->processReferral(
                $validated['referral_code'],
                $validated['referrer_id'],
                $validated['referee_id']
            );
            return response()->json($referral, 201);
        } catch (WalletException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Complete a referral (process rewards)
     *
     * @param string $referralId
     * @return JsonResponse
     */
    public function completeReferral(string $referralId): JsonResponse
    {
        try {
            $referral = $this->referralService->completeReferral($referralId);
            return response()->json($referral);
        } catch (WalletException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Get all active referral programs
     * (This would need to be implemented in your ReferralRepository)
     *
     * @return JsonResponse
     */
    public function getActivePrograms(): JsonResponse
    {
        try {
            // This assumes you'll add a method to your service/repository
            // to get active programs. Here's how it might be implemented:
            $activePrograms = $this->referralService->getActivePrograms();
            return response()->json($activePrograms);
        } catch (WalletException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
