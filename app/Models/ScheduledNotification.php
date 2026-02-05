<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledNotification extends Model
{
    use HasFactory;

    protected $table = 'scheduled_notifications';

    protected $fillable = [
        'user_id',
        'festival_id',
        'title',
        'message',
        'scheduled_at',
        'status',
        'sent_at'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    /**
     * Get the user that owns the scheduled notification
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the festival for the notification
     */
    public function festival()
    {
        return $this->belongsTo(Festival::class);
    }
}
