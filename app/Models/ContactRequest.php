<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactRequest extends Model
{
    protected $fillable = [
        'locale', 'request_type', 'product_id', 'product_name', 'product_sku', 'quantity',
        'name', 'company', 'email', 'phone', 'message',
        'status', 'ip_address', 'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
