<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $keyType = 'uuid';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'wallet_type_id',
        'available_balance',
        'pending_balance',
        'total_earned',
        'total_spent',
        'wallet_tag'
    ];

    protected $casts = [
        'available_balance' => 'decimal:4',
        'pending_balance' => 'decimal:4',
        'total_earned' => 'decimal:4',
        'total_spent' => 'decimal:4',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function walletType()
    {
        return $this->belongsTo(WalletType::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function ledgers()
    {
        return $this->hasMany(WalletLedger::class);
    }

    public function requests()
    {
        return $this->hasMany(Request::class);
    }
}
