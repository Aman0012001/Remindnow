<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Festival extends Model
{
    use HasFactory;
    protected $table = 'festivals';

    protected $fillable = [
        'name',
        'date',
        'event_date',
        'image',
        'temple_id',
        'states',
        'is_active',
        'is_deleted',
        'is_popular'
    ];

    protected $casts = [
        'states' => 'array',
        'event_date' => 'date:Y-m-d'
    ];

    public function temple()
    {
        return $this->hasMany(Temple::class, 'id', 'id');
        // hasOne(RelatedModel::class, foreign_key_on_related_table, local_key_on_this_model)
    }

    public function faqs()
    {
        return $this->hasMany(Faq::class, 'is_festival', 'id');
        // hasOne(RelatedModel::class, foreign_key_on_related_table, local_key_on_this_model)
    }

    public function festivalDesc()
    {
        return $this->hasOne(FestivalDescription::class, 'parent_id', 'id')->where('language_id', currentLangId());
        // hasOne(RelatedModel::class, foreign_key_on_related_table, local_key_on_this_model)
    }
}
