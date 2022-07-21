<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'extId',
        'title',
        'imageId',
    ];

    public function image()
    {
        return $this->belongsTo(
            Image::class,
            'imageId',
            'id',
        );
    }

    public function getImageAttribute()
    {
        return $this->image()->first();
    }
}
