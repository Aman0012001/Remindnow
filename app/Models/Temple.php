<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Temple extends Model
{
    use HasFactory;
    protected $table = 'temples';
    
    public function templeDesc()
    {
        return $this->hasOne(TempleDescription::class, 'parent_id', 'id')->where('language_id',currentLangId());
        // hasOne(RelatedModel::class, foreign_key_on_related_table, local_key_on_this_model)
    }
}
