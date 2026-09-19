<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsArticle extends Model
{
    protected $fillable = [
        'slug', 'status', 'show_on_homepage', 'sort_order', 'published_at', 'translations', 'images',
    ];

    protected function casts(): array
    {
        return [
            'show_on_homepage' => 'boolean',
            'published_at' => 'datetime',
            'translations' => 'array',
            'images' => 'array',
        ];
    }
}
