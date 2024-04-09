<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    public function media()
    {
        return $this->belongsTo(Media::class);
    }
}
