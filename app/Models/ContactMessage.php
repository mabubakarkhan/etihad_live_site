<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    public const STATUS_NEW = 'new';
    public const STATUS_SEEN = 'seen';

    protected $fillable = ['name', 'email', 'phone', 'message', 'status', 'seen_at'];

    protected $casts = ['seen_at' => 'datetime'];
}

