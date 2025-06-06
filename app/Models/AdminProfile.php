<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminProfile extends Model
{
    use HasFactory;

    protected $primaryKey = 'admin_id';
    protected $keyType = 'uuid';
    public $incrementing = false;

    protected $fillable = ['position', 'department'];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
