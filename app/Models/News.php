<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    //

    public function details()
    {
        return $this->hasMany(NewsDetails::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
