<?php

namespace App\Contracts;

interface ReferralServiceInterface
{
    public function createReferralProgram(array $data): array;
    public function processReferral(string $referralCode, string $referrerId, string $refereeId): array;
    public function completeReferral(string $referralId): array;
    public function getReferralProgram(string $programId): array;
    public function getUserReferrals(string $userId): array;
}
