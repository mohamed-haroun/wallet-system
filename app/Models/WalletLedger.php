<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletLedger extends Model
{
    use HasFactory;

    protected $keyType = 'uuid';
    public $incrementing = false;

    protected $fillable = [
        'wallet_id',
        'balance_before',
        'balance_after',
        'amount',
        'reference'
    ];

    protected $casts = [
        'balance_before' => 'decimal:4',
        'balance_after' => 'decimal:4',
        'amount' => 'decimal:4',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
