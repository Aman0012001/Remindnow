<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    protected $table = 'states';

    protected $fillable = [
        'name',
        'is_active',
        'is_deleted'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_deleted' => 'boolean',
    ];

    /**
     * Scope to get active states
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * Scope to get non-deleted states
     */
    public function scopeNotDeleted($query)
    {
        return $query->where('is_deleted', 0);
    }

    /**
     * Get only active and non-deleted states
     */
    public function scopeActiveAndNotDeleted($query)
    {
        return $query->where('is_active', 1)->where('is_deleted', 0);
    }
}