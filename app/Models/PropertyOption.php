<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'stringVal',
        'textVal',
        'numberVal',
        'floatVal',
        'propertyId',
        'sectionId',
    ];

    public function getValueAttribute()
    {
        if ($this->floatVal) {
            return (float)$this->floatVal;
        } elseif ($this->numberVal) {
            return (int)$this->numberVal;
        } elseif ($this->stringVal) {
            return (string)$this->stringVal;
        } elseif ($this->textVal) {
            return (string)$this->textVal;
        }

        return null;
    }

    public function getPropertyAttribute()
    {
        return Property::find($this->propertyId);
    }

    public function setValue($propertyId, $value)
    {
        $property = Property::find($propertyId);

        switch ($property->valueType) {
            case 'float':
                $this->floatVal = (float)$value;
                break;
            case 'number':
                $this->numberVal = (int)$value;
                break;
            case 'string':
                $this->stringVal = (string)$value;
                break;
            case 'text':
                $this->textVal = (string)$value;
                break;
        }
    }
}
