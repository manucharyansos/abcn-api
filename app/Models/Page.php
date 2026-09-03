<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['slug', 'status', 'content', 'meta'];

    protected function casts(): array
    {
        return ['content' => 'array', 'meta' => 'array'];
    }
}
