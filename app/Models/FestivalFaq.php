<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FestivalFaq extends Model
{
    use HasFactory;
    protected $table = 'festival_faqs';
    
    public function festival()
    {
        return $this->belongsTo(Festival::class, 'festival_id');
    }
}
