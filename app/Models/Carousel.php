<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carousel extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'model',
        'modelId',
        'imageId',
        'mobileImage',
    ];

    public function image()
    {
        return $this->belongsTo(
            Image::class,
            'imageId',
            'id'
        );
    }

    public function mobile()
    {
        return $this->belongsTo(
            Image::class,
            'mobileImage',
            'id'
        );
    }

    public function relation()
    {
        if ($this->model && $this->modelId) {
            return $this->model::find($this->modelId);
        }

        return null;
    }

    public function getImageAttribute()
    {
        return $this->image()->first();
    }

    public function getMobileAttribute()
    {
        return $this->mobile()->first();
    }

    public function getRelationAttribute()
    {
        return $this->relation();
    }
}
