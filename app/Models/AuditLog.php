<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $keyType = 'uuid';
    public $incrementing = false;

    protected $fillable = [
        'event',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'user_id',
        'user_type',
        'ip_address',
        'user_agent',
        'tags'
    ];

    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
    ];

    public function auditable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->morphTo('user');
    }
}
