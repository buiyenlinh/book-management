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
        'page_total',
        'cover_image',
        'producer',
        'author',
        'content',
        'mp3',
        'category_id',
        'status',
        'username'
    ];
}
