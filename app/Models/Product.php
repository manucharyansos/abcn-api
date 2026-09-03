<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'product_category_id', 'slug', 'sku', 'status', 'featured', 'sort_order',
        'translations', 'specifications', 'images', 'documents',
    ];

    protected function casts(): array
    {
        return [
            'featured' => 'boolean',
            'translations' => 'array',
            'specifications' => 'array',
            'images' => 'array',
            'documents' => 'array',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }
}
