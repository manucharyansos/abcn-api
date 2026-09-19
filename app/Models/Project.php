<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'slug', 'status', 'show_on_homepage', 'sort_order', 'completed_at', 'translations', 'images',
    ];

    protected function casts(): array
    {
        return [
            'show_on_homepage' => 'boolean',
            'completed_at' => 'date:Y-m-d',
            'translations' => 'array',
            'images' => 'array',
        ];
    }
}
