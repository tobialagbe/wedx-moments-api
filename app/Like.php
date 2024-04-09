<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    public function media()
    {
        return $this->belongsTo(Media::class);
    }
}
