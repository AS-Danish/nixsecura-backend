<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'specialization',
        'bio',
        'experience',
        'image',
        'email',
        'phone',
        'qualifications',
        'expertise_areas',
        'order',
        'is_active',
    ];

    protected $casts = [
        'qualifications' => 'array',
        'expertise_areas' => 'array',
        'order' => 'integer',
        'is_active' => 'boolean',
    ];
}
