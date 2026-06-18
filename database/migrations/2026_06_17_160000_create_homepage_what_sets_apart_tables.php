<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @return array<int, array{sort_order: int, title: string, description: string, icon_svg: string}>
     */
    private function seedCards(): array
    {
        return [
            [
                'sort_order' => 1,
                'title' => 'Expert Knowledge',
                'description' => "With years of experience in Pakistan's real estate market, we bring deep insights and strategic guidance to every project.",
                'icon_svg' => '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="32" cy="32" r="30" stroke="currentColor" stroke-width="2"/><path d="M32 12v20m10-10H22" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',
            ],
            [
                'sort_order' => 2,
                'title' => 'Premium Quality',
                'description' => 'Every project reflects our commitment to excellence, featuring premium designs, superior construction, and thoughtful planning.',
                'icon_svg' => '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M32 12l8 16h16l-13 10 5 16-16-12-16 12 5-16-13-10h16l8-16z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>',
            ],
            [
                'sort_order' => 3,
                'title' => 'Transparent Process',
                'description' => 'We believe in full transparency with clear communication, fair pricing, and documented agreements at every step.',
                'icon_svg' => '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="12" y="20" width="40" height="28" rx="2" stroke="currentColor" stroke-width="2"/><path d="M12 28h40M20 20v-6h24v6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',
            ],
            [
                'sort_order' => 4,
                'title' => '24/7 Support',
                'description' => 'Our dedicated customer support team is always available to address your queries and concerns promptly.',
                'icon_svg' => '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M32 12c11.05 0 20 8.95 20 20s-8.95 20-20 20-20-8.95-20-20 8.95-20 20-20z" stroke="currentColor" stroke-width="2"/><path d="M32 22v14l10 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            ],
            [
                'sort_order' => 5,
                'title' => 'Strategic Locations',
                'description' => 'Our projects are strategically positioned in high-demand areas across Lahore and Pakistan for maximum value appreciation.',
                'icon_svg' => '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 32h40M32 12v40M52 32c0 11.05-8.95 20-20 20s-20-8.95-20-20 8.95-20 20-20 20 8.95 20 20z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',
            ],
            [
                'sort_order' => 6,
                'title' => 'Award Winning',
                'description' => 'Recognized for our contributions to real estate innovation and customer satisfaction across Pakistan.',
                'icon_svg' => '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M32 12l6 15h16l-13 10 5 16-16-12-16 12 5-16-13-10h16l6-15z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>',
            ],
        ];
    }

    public function up(): void
    {
        Schema::create('homepage_what_sets_apart_settings', function (Blueprint $table) {
            $table->id();
            $table->string('title_line_1')->nullable();
            $table->string('title_highlight')->nullable();
            $table->text('subtitle')->nullable();
            $table->timestamps();
        });

        Schema::create('homepage_what_sets_apart_cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('title');
            $table->text('description');
            $table->text('icon_svg')->nullable();
            $table->string('icon_image')->nullable();
            $table->timestamps();
        });

        DB::table('homepage_what_sets_apart_settings')->insert([
            'title_line_1' => 'What',
            'title_highlight' => 'Set Us Apart?',
            'subtitle' => 'At Etihad Marketing, we combine expertise, innovation, and customer-centric solutions to deliver exceptional real estate experiences in Pakistan.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $now = now();
        foreach ($this->seedCards() as $card) {
            DB::table('homepage_what_sets_apart_cards')->insert([
                'sort_order' => $card['sort_order'],
                'title' => $card['title'],
                'description' => $card['description'],
                'icon_svg' => $card['icon_svg'],
                'icon_image' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_what_sets_apart_cards');
        Schema::dropIfExists('homepage_what_sets_apart_settings');
    }
};
