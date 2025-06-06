<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    protected $keyType = 'uuid';
    public $incrementing = false;

    protected $fillable = [
        'request_type_id',
        'user_id',
        'wallet_id',
        'amount',
        'details',
        'status_id',
        'processed_by',
        'processed_at',
        'admin_notes',
        'rejection_reason',
        'attachments'
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'attachments' => 'json',
        'processed_at' => 'datetime',
    ];

    public function requestType()
    {
        return $this->belongsTo(RequestType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function status()
    {
        return $this->belongsTo(TransactionStatus::class, 'status_id');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function approvals()
    {
        return $this->hasMany(RequestApproval::class);
    }
}
