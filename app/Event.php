<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'user_id', 'name', 'cover_image', 'visibility',
    ];

    public function media()
    {
        return $this->hasMany(Media::class);
    }
}
