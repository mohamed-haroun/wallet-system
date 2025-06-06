<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $keyType = 'uuid';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'email',
        'password',
        'referral_code',
        'referred_by',
        'user_type_id',
        'phone',
        'is_active'
    ];

    protected $hidden = ['password', 'remember_token'];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'id' => 'string', // Cast UUID as string
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::orderedUuid()->toString();
            }

            if (empty($model->referral_code)) {
                $model->referral_code = Str::upper(Str::random(8));
            }
        });
    }

    public function userType()
    {
        return $this->belongsTo(UserType::class);
    }

    public function isAdmin()
    {
        return $this->userType->name === 'admin';
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function referees()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

    public function adminProfile()
    {
        return $this->hasOne(AdminProfile::class, 'admin_id');
    }

    public function roles()
    {
        return $this->morphToMany(Role::class, 'model', 'model_has_roles');
    }

    public function permissions()
    {
        return $this->morphToMany(Permission::class, 'model', 'model_has_permissions');
    }

    public function transactions()
    {
        return $this->hasManyThrough(Transaction::class, Wallet::class);
    }

    public function requests()
    {
        return $this->hasMany(Request::class, 'user_id');
    }
}
