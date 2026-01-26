<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'image',
        'category',
        'read_time',
        'published_at',
        'author_name',
        'author_image',
        'author_role',
        'tags',
    ];

    protected $casts = [
        'tags' => 'array',
        'published_at' => 'date',
    ];
}
