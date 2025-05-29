<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyFavoriteSubject extends Model
{
    /** @use HasFactory<\Database\Factories\MyFavoriteSubjectFactory> */
    use HasFactory;

    protected $table = 'my_favorite_subject';
    
    protected $guarded = [];
}


