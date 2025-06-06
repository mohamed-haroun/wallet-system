<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;

    protected $keyType = 'uuid';
    public $incrementing = false;

    protected $fillable = [
        'program_id',
        'referrer_id',
        'referee_id',
        'referrer_reward',
        'referee_reward',
        'referrer_transaction_id',
        'referee_transaction_id',
        'status'
    ];

    protected $casts = [
        'referrer_reward' => 'decimal:4',
        'referee_reward' => 'decimal:4',
    ];

    public function program()
    {
        return $this->belongsTo(ReferralProgram::class);
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referee()
    {
        return $this->belongsTo(User::class, 'referee_id');
    }

    public function referrerTransaction()
    {
        return $this->belongsTo(Transaction::class, 'referrer_transaction_id');
    }

    public function refereeTransaction()
    {
        return $this->belongsTo(Transaction::class, 'referee_transaction_id');
    }
}
