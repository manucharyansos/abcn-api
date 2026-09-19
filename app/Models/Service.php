<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'slug', 'status', 'show_on_homepage', 'sort_order', 'translations', 'images',
    ];

    protected function casts(): array
    {
        return [
            'show_on_homepage' => 'boolean',
            'translations' => 'array',
            'images' => 'array',
        ];
    }
}
