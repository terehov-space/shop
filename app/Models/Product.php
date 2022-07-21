<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'code',
        'extId',
        'bitrixExtId',
        'vendorCode',
        'vendorId',
        'title',
        'description',
        'price',
        'showPrice',
        'priceEur',
        'priceUsd',
        'updateEur',
        'updateUsd',
        'active',
        'showMain',
        'seoTitle',
        'seoDescription',
        'sectionId',
        'imageId',
        'syncCometa',
        'onOrder',
    ];

    public function section()
    {
        return $this->belongsTo(
            Section::class,
            'sectionId',
            'id'
        );
    }


    public function sections()
    {
        return $this->hasMany(
            ProductToSection::class,
            'productId',
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

    public function images()
    {
        return $this->hasMany(
            ImageToProduct::class,
            'productId',
            'id'
        );
    }

    public function files()
    {
        return $this->hasMany(
            FileToProduct::class,
            'productId',
            'id'
        );
    }

    public function vendor()
    {
        return $this->belongsTo(
            Vendor::class,
            'vendorId',
            'id'
        );
    }

    public function properties()
    {
        return $this->hasMany(
            PropertyToProduct::class,
            'productId',
            'id'
        );
    }

    public function getVendorAttribute()
    {
        return $this->vendor()->first();
    }

    public function getSectionAttribute()
    {
        return $this->section()->first();
    }

    public function getSectionsAttribute()
    {
        return $this->sections()->get();
    }

    public function getImageAttribute()
    {
        return $this->image()->first();
    }

    public function getImagesAttribute()
    {
        return $this->images()->get();
    }

    public function getFilesAttribute()
    {
        return $this->files()->get();
    }

    public function getPropertiesAttribute()
    {
        return $this->properties()->get();
    }

    public function delete()
    {
        ProductToSection::where('productId', '=', $this->id)
            ->delete();

        return parent::delete();
    }
}
