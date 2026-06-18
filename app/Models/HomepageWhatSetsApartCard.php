<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageWhatSetsApartCard extends Model
{
    protected $fillable = [
        'sort_order',
        'title',
        'description',
        'icon_svg',
        'icon_image',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function iconImageUrl(): ?string
    {
        if (! is_string($this->icon_image) || trim($this->icon_image) === '') {
            return null;
        }

        return url('storage/' . ltrim($this->icon_image, '/'));
    }
}
