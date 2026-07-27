<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    public const STATUS_NEW = 'new';
    public const STATUS_SEEN = 'seen';

    public const SOURCE_CONTACT_FORM = 'contact_form';
    public const SOURCE_POPUP_FIRST_VISITOR = 'popup_first_visitor';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'city',
        'message',
        'status',
        'source',
        'seen_at',
    ];

    protected $casts = ['seen_at' => 'datetime'];

    public function sourceLabel(): string
    {
        return match ($this->source) {
            self::SOURCE_POPUP_FIRST_VISITOR => 'Popup first-time visitor',
            default => 'Contact form',
        };
    }
}
