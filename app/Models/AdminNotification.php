<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    public const TYPE_PROPERTY_REQUEST = 'property_request';
    public const TYPE_PROJECT_REQUEST = 'project_request';
    public const TYPE_JOB_APPLICATION = 'job_application';

    protected $fillable = [
        'type',
        'title',
        'body',
        'link',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }

    public static function notify(string $type, string $title, ?string $body = null, ?string $link = null): self
    {
        return static::create([
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'link' => $link,
        ]);
    }
}
