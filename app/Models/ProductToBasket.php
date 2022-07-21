<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductToBasket extends Model
{
    use HasFactory;

    protected $fillable = [
        'price',
        'count',
        'productId',
        'basketId',
    ];

    public function product()
    {
        return $this->belongsTo(
            Product::class,
            'productId',
            'id'
        );
    }

    public function getProductAttribute()
    {
        return $this->product()->withTrashed()->first();
    }
}
