<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'image'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'image' => 'string'
    ];

    protected $with = ['roles', 'user_only'];

    public function getRoleAttribute()
    {
        return $this->roles->first()->name ?? null;
    }

    /**
     * Relación con la información de OnlyFans
     */
    public function user_only(): HasOne
    {
        return $this->hasOne(UserOnly::class);
    }
}