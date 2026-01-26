<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workshop extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'image',
        'date',
        'start_time',
        'end_time',
        'location',
        'max_participants',
        'registrations',
        'status',
        'price',
        'instructors',
    ];

    protected $casts = [
        'date' => 'date',
        'instructors' => 'array',
        'price' => 'decimal:2',
        'max_participants' => 'integer',
        'registrations' => 'integer',
    ];
}
