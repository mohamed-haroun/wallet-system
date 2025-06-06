<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Controllers\Controller;
use App\Services\Wallet\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReferralController extends Controller
{
    protected $referralService;

    public function __construct(ReferralService $referralService)
    {
        $this->referralService = $referralService;
    }

    // GET /api/referral-programs/{id}
    public function showProgram(string $id)
    {
        $program = $this->referralService->getReferralProgram($id);
        return response()->json($program);
    }

    // GET /api/users/{userId}/referrals
    public function getUserReferrals(string $userId)
    {
        $referrals = $this->referralService->getUserReferrals($userId);
        return response()->json($referrals);
    }

    // POST /api/referral-programs
    public function createProgram(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'code' => 'required|string|unique:referral_programs,code',
            'referrer_reward' => 'required|numeric',
            'referee_reward' => 'required|numeric',
            'reward_type' => 'nullable|string|in:fixed,percentage',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'nullable|boolean',
        ]);

        $program = $this->referralService->createReferralProgram($data);
        return response()->json($program, 201);
    }

    // POST /api/referrals
    public function createReferral(Request $request)
    {
        $data = $request->validate([
            'referral_code' => 'required|string',
            'referrer_id' => 'required|uuid',
            'referee_id' => 'required|uuid|different:referrer_id',
        ]);

        try {
            $referral = $this->referralService->processReferral(
                $data['referral_code'],
                $data['referrer_id'],
                $data['referee_id']
            );
            return response()->json($referral, 201);
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'referral' => [$e->getMessage()],
            ]);
        }
    }

    // POST /api/referrals/{id}/complete
    public function completeReferral(string $id)
    {
        try {
            $referral = $this->referralService->completeReferral($id);
            return response()->json($referral);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
