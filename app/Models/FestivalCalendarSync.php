<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FestivalCalendarSync extends Model
{
    use HasFactory;

    protected $table = 'festival_calendar_syncs';

    protected $fillable = [
        'user_id',
        'festival_id',
        'google_event_id',
        'calendar_id',
        'synced_at'
    ];

    protected $casts = [
        'synced_at' => 'datetime',
    ];

    /**
     * Get the user that owns the sync
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the festival that was synced
     */
    public function festival()
    {
        return $this->belongsTo(Festival::class);
    }
}
