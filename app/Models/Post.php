<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;  

    protected $guarded = [];

    protected $appends = ['created_at_for_humans'];

    public function comments()
    {
        return $this->hasMany(Commetn::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function getCreatedAtForHumansAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}
