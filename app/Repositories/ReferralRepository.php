<?php

namespace App\Repositories;

use App\Models\ReferralProgram;
use App\Models\Referral;

class ReferralRepository
{
    public function createProgram(array $data): ReferralProgram
    {
        return ReferralProgram::create($data);
    }

    public function findActiveProgramByCode(string $code): ?ReferralProgram
    {
        return ReferralProgram::where('code', $code)
            ->where('is_active', true)
            ->whereDate('start_date', '<=', now())
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', now());
            })
            ->first();
    }

    public function referralExists(int $programId, string $referrerId, string $refereeId): bool
    {
        return Referral::where('program_id', $programId)
            ->where('referrer_id', $referrerId)
            ->where('referee_id', $refereeId)
            ->exists();
    }

    public function create(array $data): Referral
    {
        return Referral::create($data);
    }

    public function find(string $referralId): Referral
    {
        return Referral::with(['program', 'referrer', 'referee', 'referrerTransaction', 'refereeTransaction'])
            ->findOrFail($referralId);
    }

    public function update(string $referralId, array $data): Referral
    {
        $referral = Referral::findOrFail($referralId);
        $referral->update($data);
        return $referral;
    }

    public function getUserReferrals(string $userId)
    {
        return Referral::with(['program', 'referee', 'referrerTransaction'])
            ->where('referrer_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getProgramReferrals(int $programId)
    {
        return Referral::with(['referrer', 'referee'])
            ->where('program_id', $programId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findProgram(string $programId): ReferralProgram
    {
        return ReferralProgram::withCount(['referrals', 'referrals as completed_referrals' => function ($query) {
            $query->where('status', 'completed');
        }])->findOrFail($programId);
    }
}
