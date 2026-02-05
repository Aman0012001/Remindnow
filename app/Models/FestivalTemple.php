<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FestivalTemple extends Model
{
    use HasFactory;
    protected $table = 'festivals_temple';
    
    public function temple()
    {
        return $this->belongsTo(Temple::class, 'temple_id');
        // hasOne(RelatedModel::class, foreign_key_on_related_table, local_key_on_this_model)
    }
}
