<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductToSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'sectionId',
        'productId',
    ];

    public function section()
    {
        return $this->belongsTo(
            Section::class,
            'sectionId',
            'id'
        );
    }

    public function getSectionAttribute()
    {
        return $this->section()->first();
    }
}
