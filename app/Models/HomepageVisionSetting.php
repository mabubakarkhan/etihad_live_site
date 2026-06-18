<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageVisionSetting extends Model
{
    protected $fillable = [
        'tagline',
        'heading_line_1',
        'heading_line_2',
        'ceo_image',
        'message_paragraph_1',
        'message_paragraph_2_highlight',
        'message_paragraph_2_body',
        'ceo_name',
        'ceo_title',
    ];

    /**
     * @return array<string, string>
     */
    public static function defaultAttributes(): array
    {
        return [
            'tagline' => 'MESSAGE FROM OUR CEO',
            'heading_line_1' => 'A VISION FOR',
            'heading_line_2' => 'EXCELLENCE',
            'ceo_image' => null,
            'message_paragraph_1' => '"Over the years, Etihad Marketing has built a reputation for being a leading real estate firm and I take great pride in the long-term relationships we have forged, highlighting the strengths within our core values, and culture of the business."',
            'message_paragraph_2_highlight' => '"Our team of dedicated professionals',
            'message_paragraph_2_body' => 'brings passion and precision to every detail, ensuring that your project not only meets but exceeds expectations. Thank you for trusting us with your vision."',
            'ceo_name' => 'Zeeshan Ahsan Butt',
            'ceo_title' => 'CEO & Co-Founder',
        ];
    }

    public static function instance(): self
    {
        $row = static::query()->first();
        if ($row) {
            return $row;
        }

        return static::query()->create(static::defaultAttributes());
    }
}
