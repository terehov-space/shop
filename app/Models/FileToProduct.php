<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileToProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'productId',
        'fileId',
    ];

    protected $appends = [
        'file',
    ];

    public function file()
    {
        return $this->belongsTo(
            File::class,
            'fileId',
            'id'
        );
    }

    public function getFileAttribute()
    {
        return $this->file()->first();
    }
}
