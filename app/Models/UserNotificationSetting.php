<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotificationSetting extends Model
{
    use HasFactory;

    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'notification_type',
        'email_enabled',
        'sms_enabled',
        'push_enabled'
    ];

    protected $casts = [
        'email_enabled' => 'boolean',
        'sms_enabled' => 'boolean',
        'push_enabled' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
