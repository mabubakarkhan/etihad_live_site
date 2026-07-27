<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FirstVisitPopupSetting extends Model
{
    protected $fillable = [
        'is_enabled',
        'force_show_every_time',
        'eyebrow',
        'heading',
        'subheading',
        'body_text',
        'cta_label',
        'form_heading',
        'form_submit_label',
        'background_image',
        'show_logo',
        'delay_ms',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'force_show_every_time' => 'boolean',
            'show_logo' => 'boolean',
            'delay_ms' => 'integer',
        ];
    }

    public static function instance(): self
    {
        $row = static::query()->first();
        if ($row) {
            return $row;
        }

        return static::query()->create([
            'is_enabled' => true,
            'force_show_every_time' => false,
            'eyebrow' => 'DHA PHASE X — THE FUTURE OF LAHORE',
            'heading' => 'We proudly unveil Phase-X – “The Future of Lahore”',
            'subheading' => 'The journey of long-term vision of DHA Lahore begins in August 2026.',
            'body_text' => 'Designed around integrated living, business, mobility, green infrastructure & long-term service standards.',
            'cta_label' => 'Contact Us',
            'form_heading' => 'Get in touch with Etihad',
            'form_submit_label' => 'Submit',
            'show_logo' => true,
            'delay_ms' => 0,
        ]);
    }

    public function backgroundImageUrl(): ?string
    {
        $path = trim((string) ($this->background_image ?? ''));
        if ($path === '') {
            return null;
        }

        return asset('storage/' . ltrim($path, '/'));
    }

    /** @return array<string, mixed> */
    public function toFrontPayload(): array
    {
        return [
            'enabled' => (bool) $this->is_enabled,
            'forceShow' => (bool) $this->force_show_every_time,
            'eyebrow' => trim((string) ($this->eyebrow ?? '')),
            'heading' => trim((string) ($this->heading ?? '')),
            'subheading' => trim((string) ($this->subheading ?? '')),
            'body' => trim((string) ($this->body_text ?? '')),
            'cta' => trim((string) ($this->cta_label ?? '')) ?: 'Contact Us',
            'formHeading' => trim((string) ($this->form_heading ?? '')) ?: 'Get in touch',
            'formSubmit' => trim((string) ($this->form_submit_label ?? '')) ?: 'Submit',
            'bg' => $this->backgroundImageUrl(),
            'showLogo' => (bool) $this->show_logo,
            'logo' => asset('theme/images/logo.png'),
            'delayMs' => max(0, min(5000, (int) ($this->delay_ms ?? 0))),
            'submitUrl' => route('first-visit-popup.submit'),
            'trackUrl' => route('site-analytics.track'),
        ];
    }
}
