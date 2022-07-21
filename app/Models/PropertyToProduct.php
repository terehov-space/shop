<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyToProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'productId',
        'propertyId',
        'optionId',
        'sectionId',
        'modelId',
    ];

    public function property()
    {
        return $this->belongsTo(
            Property::class,
            'propertyId',
            'id',
        );
    }

    public function option()
    {
        return $this->belongsTo(
            PropertyOption::class,
            'optionId',
            'id'
        );
    }

    public function getPropertyAttribute()
    {
        return $this->property()->first();
    }

    public function getOptionAttribute()
    {
        return $this->option()->first();
    }

    public function getModelAttribute()
    {
        if ($this->modelId && $this->property->model) {
            return $this->property->model::find($this->modelId);
        }

        return null;
    }
}
