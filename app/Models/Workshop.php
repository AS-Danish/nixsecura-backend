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
        // 'image', // Removed as we use workshop_images table now
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

    protected $with = ['images']; // Eager load images by default

    public function images()
    {
        return $this->hasMany(WorkshopImage::class);
    }

    // Accessor for backward compatibility
    public function getImageAttribute()
    {
        return $this->images->first()?->image_path;
    }
}
