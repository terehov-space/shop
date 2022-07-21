<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'extId',
        'bitrixExtId',
        'title',
        'active',
        'showMain',
        'seoTitlePostfix',
        'seoDescription',
        'sectionId',
        'imageId',
        'sectionId',
    ];

    public function sections()
    {
        return $this->hasMany(
            Section::class,
            'sectionId',
            'id'
        );
    }

    public function section()
    {
        return $this->belongsTo(
            Section::class,
            'sectionId',
            'id'
        );
    }

    public function image()
    {
        return $this->belongsTo(
            Image::class,
            'imageId',
            'id'
        );
    }

    public function getSectionsAttribute()
    {
        return $this->sections()->get();
    }

    public function getSectionAttribute()
    {
        return $this->section()->first();
    }

    public function getImageAttribute()
    {
        return $this->image()->first();
    }

    public function product()
    {
        return $this->hasMany(
            Product::class,
            'sectionId',
            'id'
        );
    }

    public function products()
    {
        return $this->hasMany(
            ProductToSection::class,
            'sectionId',
            'id'
        );
    }

    public function scopeProductable($query)
    {
        return $query->whereHas('product')
            ->orWhereHas('products');
    }

    public static function findRootSection($section)
    {
        if ($section->sectionId) {
            return self::findRootSection($section->section);
        }

        return $section;
    }

    public function getLevelAttribute()
    {
        if (!$this->section) {
            return 1;
        } elseif ($this->section->section) {
            return 3;
        } else {
            return 2;
        }
    }
}
