<?php

namespace App\Http\Controllers;

use App\Services\Wallet\ReferralService;
use Illuminate\Http\Request;
use App\Exceptions\WalletException;

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
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProgram($programId)
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserReferrals(Request $request, $userId)
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function createProgram(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:referral_programs,code',
            'referrer_reward' => 'required|numeric|min:0',
            'referee_reward' => 'required|numeric|min:0',
            'reward_type' => 'nullable|in:fixed,percentage',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        try {
            $program = $this->referralService->createReferralProgram($validated);
            return response()->json($program, 201);
        } catch (WalletException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Process a new referral
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processReferral(Request $request)
    {
        $validated = $request->validate([
            'referral_code' => 'required|string|exists:referral_programs,code',
            'referrer_id' => 'required|exists:users,id',
            'referee_id' => 'required|exists:users,id',
        ]);

        try {
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function completeReferral($referralId)
    {
        try {
            $referral = $this->referralService->completeReferral($referralId);
            return response()->json($referral);
        } catch (WalletException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Get all referral programs
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            // Assuming we'll add a getAllPrograms method to the service
            $programs = $this->referralService->getAllPrograms();
            return response()->json($programs);
        } catch (WalletException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Update a referral program
     *
     * @param Request $request
     * @param string $programId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProgram(Request $request, $programId)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|max:255|unique:referral_programs,code,' . $programId,
            'referrer_reward' => 'sometimes|numeric|min:0',
            'referee_reward' => 'sometimes|numeric|min:0',
            'reward_type' => 'sometimes|in:fixed,percentage',
            'start_date' => 'sometimes|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'sometimes|boolean',
        ]);

        try {
            // Assuming we'll add an updateProgram method to the service
            $program = $this->referralService->updateProgram($programId, $validated);
            return response()->json($program);
        } catch (WalletException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Get referral statistics
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats()
    {
        try {
            // Assuming we'll add a getStats method to the service
            $stats = $this->referralService->getStats();
            return response()->json($stats);
        } catch (WalletException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
