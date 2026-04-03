<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    protected $table = 'personal_access_tokens';

    protected $fillable = [
        'name',
        'token',
        'abilities',
        'expires_at',
    ];

    protected $casts = [
        'abilities'    => 'json',
        'last_used_at' => 'datetime',
        'expires_at'   => 'datetime',
    ];

    // Relación polimórfica → apunta al modelo que tenga el token
    public function tokenable()
    {
        return $this->morphTo();
    }
}