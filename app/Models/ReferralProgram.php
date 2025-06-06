<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'referrer_reward',
        'referee_reward',
        'reward_type',
        'start_date',
        'end_date',
        'is_active'
    ];

    protected $casts = [
        'referrer_reward' => 'decimal:4',
        'referee_reward' => 'decimal:4',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function referrals()
    {
        return $this->hasMany(Referral::class);
    }
}
