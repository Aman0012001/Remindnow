<?php
// app/Models/UserNotification.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'device_id',
        'user_id',
        'is_sent',
        'is_active',
        'is_deleted'
    ];

    protected $casts = [
        'is_sent' => 'boolean',
        'is_active' => 'boolean',
        'is_deleted' => 'boolean'
    ];

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get active notifications
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where('is_deleted', false);
    }

    /**
     * Scope to get notifications for specific user
     */
    public function scopeForUser($query, $userId = null)
    {
        if ($userId) {
            return $query->where('user_id', $userId);
        }
        return $query;
    }

    /**
     * Scope to get notifications for specific device
     */
    public function scopeForDevice($query, $deviceId = null)
    {
        if ($deviceId) {
            return $query->where('device_id', $deviceId);
        }
        return $query;
    }

    /**
     * Scope to get unsent notifications
     */
    public function scopeUnsent($query)
    {
        return $query->where('is_sent', false);
    }
}