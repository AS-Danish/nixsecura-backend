<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    protected $table = 'gallery';

    protected $fillable = [
        'title',
        'category',
        'image',
        'description',
        'order',
        'is_featured',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_featured' => 'boolean',
    ];
}
