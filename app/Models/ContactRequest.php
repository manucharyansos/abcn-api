<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactRequest extends Model
{
    protected $fillable = [
        'locale', 'name', 'company', 'email', 'phone', 'message',
        'status', 'ip_address', 'user_agent',
    ];
}
