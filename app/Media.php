<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

}
