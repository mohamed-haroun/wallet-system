<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'name',
        'display_name',
        'description',
        'guard_name'
    ];

    public function group()
    {
        return $this->belongsTo(PermissionGroup::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_has_permissions');
    }
}
