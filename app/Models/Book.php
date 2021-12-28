<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'describe',
        'language',
        'release_year',
        'cover_image',
        'producer',
        'author_id',
        'mp3',
        'category_id',
        'status',
        'free',
        'username',
        'alias'
    ];
}
