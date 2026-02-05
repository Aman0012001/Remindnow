<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDeviceToken extends Model
{
    use HasFactory;

    protected $table = 'userdevicetokens';

    protected $fillable = [
        'user_id',
        'device_type',
        'device_id',
        'device_token',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
