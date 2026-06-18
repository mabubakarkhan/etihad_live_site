<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class DummyTestimonialsSeeder extends Seeder
{
    public function run(): void
    {
        if (Testimonial::query()->count() > 0) {
            return;
        }

        $dealerImages = [
            'dealers/EJNgg9BgAswwhxxq5x6ROLRjkNphCiUGUf4VLFkg.jpg',
            'dealers/HnvgfFCZXm9zdwBDxlQ99zLBenPGKw6UlzDiHRjr.png',
        ];

        $items = [
            [
                'name' => 'Ahmed R.',
                'comment' => 'Etihad Marketing made our first property purchase smooth and transparent. The team guided us at every step and we found the perfect home in Lahore.',
                'city' => 'Lahore, Punjab',
            ],
            [
                'name' => 'Fatima S.',
                'comment' => 'Professional service from start to finish. They answered all our questions quickly and helped us secure a great investment plot with flexible payment options.',
                'city' => 'Karachi, Sindh',
            ],
            [
                'name' => 'Usman K.',
                'comment' => 'Highly recommended for anyone looking for trusted real estate advice. The site visit was well organized and the documentation process was very clear.',
                'city' => 'Islamabad',
            ],
            [
                'name' => 'Ayesha M.',
                'comment' => 'We booked our unit in Etihad Town Phase 1 after comparing several projects. The sales team was honest, responsive, and truly cared about our needs.',
                'city' => 'Lahore, Punjab',
            ],
            [
                'name' => 'Hassan A.',
                'comment' => 'Excellent experience overall. From the initial inquiry to final booking, everything was handled professionally. Great value and prime location.',
                'city' => 'Multan, Punjab',
            ],
            [
                'name' => 'Sara T.',
                'comment' => 'The team helped us choose the right plan for our budget. Communication was excellent and we always felt supported throughout the process.',
                'city' => 'Rawalpindi, Punjab',
            ],
            [
                'name' => 'Bilal N.',
                'comment' => 'Outstanding customer service and reliable project information. I would definitely recommend Etihad Marketing to friends and family.',
                'city' => 'Faisalabad, Punjab',
            ],
        ];

        $now = now();

        foreach ($items as $index => $item) {
            Testimonial::query()->create([
                'name' => $item['name'],
                'image' => $dealerImages[$index % count($dealerImages)],
                'comment' => $item['comment'],
                'city' => $item['city'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
