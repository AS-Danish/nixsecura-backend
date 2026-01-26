<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'issuer',
        'year',
        'image',
        'description',
        'certificate_number',
        'issue_date',
        'expiry_date',
        'order',
        'is_featured',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'order' => 'integer',
        'is_featured' => 'boolean',
    ];
}
