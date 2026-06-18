<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactSetting extends Model
{
    protected $fillable = [
        'address',
        'latitude',
        'longitude',
        'email',
        'phone',
        'timings',
        'whatsapp',
        'facebook',
        'instagram',
        'linkedin',
        'youtube',
        'twitter',
        'tiktok',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    /**
     * Get the single contact settings instance (id = 1).
     * Creates the row with defaults if it does not exist.
     */
    public static function instance(): self
    {
        $row = static::first();
        if ($row) {
            return $row;
        }
        return static::create([
            'address' => null,
            'latitude' => null,
            'longitude' => null,
            'email' => null,
            'phone' => null,
            'whatsapp' => null,
            'facebook' => null,
            'instagram' => null,
            'linkedin' => null,
            'youtube' => null,
            'twitter' => null,
            'tiktok' => null,
        ]);
    }
}
