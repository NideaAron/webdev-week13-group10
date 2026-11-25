<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $fillable = ['caption', 'path', 'original_name', 'size'];

    public function getUrlAttribute(): string
    {
        return route('photos.image', $this->id);
    }
}