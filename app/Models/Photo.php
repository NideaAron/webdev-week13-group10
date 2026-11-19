<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    protected $fillable = ['caption', 'path', 'original_name', 'size'];

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->path);
    }
}