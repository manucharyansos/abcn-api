<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $table = 'media';

    protected $fillable = [
        'uploaded_by', 'disk', 'path', 'original_name', 'mime_type', 'kind', 'size', 'alt',
    ];

    protected $appends = ['url'];

    protected function casts(): array
    {
        return ['alt' => 'array', 'size' => 'integer'];
    }

    protected function url(): Attribute
    {
        return Attribute::get(fn () => Storage::disk($this->disk)->url($this->path));
    }
}
