<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Digital extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'imageId',
        'fileId',
        'vendorId',
    ];

    public function file()
    {
        return $this->belongsTo(
            File::class,
            'fileId',
            'id',
        );
    }

    public function getFileAttribute()
    {
        return $this->file()->first();
    }

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
