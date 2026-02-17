<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkshopImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'workshop_id',
        'image_path',
    ];

    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }
}
