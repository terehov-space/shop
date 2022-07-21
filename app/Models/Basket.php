<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Basket extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'totalPrice',
        'status',
        'email',
        'phone',
    ];

    public function products()
    {
        return $this->hasMany(
            ProductToBasket::class,
            'basketId',
            'id',
        );
    }

    public function getProductsAttribute()
    {
        return $this->products()->get();
    }
}
