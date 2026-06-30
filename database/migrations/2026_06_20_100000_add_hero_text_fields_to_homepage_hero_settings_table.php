<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('homepage_hero_settings')) {
            return;
        }

        Schema::table('homepage_hero_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('homepage_hero_settings', 'tagline')) {
                $table->string('tagline')->nullable()->after('hero_image');
            }
            if (! Schema::hasColumn('homepage_hero_settings', 'heading_line_1')) {
                $table->string('heading_line_1')->nullable()->after('tagline');
            }
            if (! Schema::hasColumn('homepage_hero_settings', 'heading_line_2')) {
                $table->string('heading_line_2')->nullable()->after('heading_line_1');
            }
            if (! Schema::hasColumn('homepage_hero_settings', 'description')) {
                $table->text('description')->nullable()->after('heading_line_2');
            }
            if (! Schema::hasColumn('homepage_hero_settings', 'cta_text')) {
                $table->string('cta_text')->nullable()->after('description');
            }
            if (! Schema::hasColumn('homepage_hero_settings', 'cta_url')) {
                $table->string('cta_url')->nullable()->after('cta_text');
            }
            if (! Schema::hasColumn('homepage_hero_settings', 'scroll_text')) {
                $table->string('scroll_text')->nullable()->after('cta_url');
            }
            if (! Schema::hasColumn('homepage_hero_settings', 'hero_image_alt')) {
                $table->string('hero_image_alt')->nullable()->after('scroll_text');
            }
        });

        $defaults = [
            'tagline' => 'We Make Passive Investing In Real Estate Simple',
            'heading_line_1' => 'BUILDING',
            'heading_line_2' => 'VISIONS',
            'description' => 'Invest in the region’s first integrated luxury and active eco-conscious development society, a project of the Defence Housing Authority expanded throughout Pakistan.',
            'cta_text' => 'Contact Us',
            'cta_url' => '/contact-us',
            'scroll_text' => 'SCROLL TO EXPLORE',
            'hero_image_alt' => 'ETIHAD hero screen 1',
        ];

        $row = DB::table('homepage_hero_settings')->first();
        if ($row) {
            $updates = [];
            foreach ($defaults as $column => $value) {
                $current = $row->{$column} ?? null;
                if ($current === null || $current === '') {
                    $updates[$column] = $value;
                }
            }
            if ($updates !== []) {
                $updates['updated_at'] = now();
                DB::table('homepage_hero_settings')->where('id', $row->id)->update($updates);
            }
        } else {
            DB::table('homepage_hero_settings')->insert(array_merge($defaults, [
                'hero_image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('homepage_hero_settings')) {
            return;
        }

        Schema::table('homepage_hero_settings', function (Blueprint $table) {
            foreach (['tagline', 'heading_line_1', 'heading_line_2', 'description', 'cta_text', 'cta_url', 'scroll_text', 'hero_image_alt'] as $column) {
                if (Schema::hasColumn('homepage_hero_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
