<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageToProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'productId',
        'imageId',
    ];

    protected $appends = [
        'image',
    ];

    public function image()
    {
        return $this->belongsTo(
            Image::class,
            'imageId',
            'id'
        );
    }

    public function getImageAttribute()
    {
        return $this->image()->first();
    }
}
