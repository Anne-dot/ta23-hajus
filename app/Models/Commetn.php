<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commetn extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $appends = ['created_at_for_humans'];

    public function getCreatedAtForHumansAttribute()
    {
        return $this->created_at ? $this->created_at->diffForHumans() : 'Unknown date';
    }
}
