<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subject',
        'content',
        'variables',
        'type',
        'is_active'
    ];

    protected $casts = [
        'variables' => 'json',
        'is_active' => 'boolean',
    ];
}
