<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGoogleToken extends Model
{
    use HasFactory;

    protected $table = 'user_google_tokens';

    protected $fillable = [
        'user_id',
        'access_token',
        'refresh_token',
        'expires_at',
        'token_type',
        'scope'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    protected $hidden = [
        'access_token',
        'refresh_token'
    ];

    /**
     * Get the user that owns the token
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if token is expired
     * 
     * @return bool
     */
    public function isExpired()
    {
        return now()->greaterThan($this->expires_at);
    }
}
