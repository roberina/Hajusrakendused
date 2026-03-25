<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shark extends Model
{
    protected $table = 'my_favorite_subject';

    protected $fillable = [
        'title', 'image', 'description',
        'max_length', 'habitat', 'danger_level', 'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}