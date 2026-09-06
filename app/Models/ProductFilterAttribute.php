<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductFilterAttribute extends Model
{
    protected $fillable = ['key', 'option', 'label', 'value', 'sort_order'];

    protected function casts(): array
    {
        return [
            'label' => 'array',
            'value' => 'array',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
