<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarEventReminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'google_event_id',
        'event_title',
        'event_date',
        'reminder_days',
        'remind_at',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
