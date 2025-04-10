<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $guarded = [];

    public function comments()
    {
        return $this->hasMany(Commetn::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
