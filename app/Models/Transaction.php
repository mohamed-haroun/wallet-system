<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $keyType = 'uuid';
    public $incrementing = false;

    protected $fillable = [
        'wallet_id',
        'transaction_type_id',
        'status_id',
        'amount',
        'fee',
        'net_amount',
        'reference',
        'external_reference',
        'narration',
        'meta',
        'ip_address',
        'device_id',
        'location',
        'reversed_at',
        'reversed_by'
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'fee' => 'decimal:4',
        'net_amount' => 'decimal:4',
        'meta' => 'json',
        'reversed_at' => 'datetime',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function transactionType()
    {
        return $this->belongsTo(TransactionType::class);
    }

    public function status()
    {
        return $this->belongsTo(TransactionStatus::class);
    }

    public function reversedBy()
    {
        return $this->belongsTo(User::class, 'reversed_by');
    }
}
