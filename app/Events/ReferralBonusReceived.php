<?php

namespace App\Events;

use App\Models\Referral;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReferralBonusReceived
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Referral $referral,
        public float $bonusAmount
    ) {}
}
