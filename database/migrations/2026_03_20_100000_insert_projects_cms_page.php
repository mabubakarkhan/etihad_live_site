<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('cms_pages')->where('slug', 'projects')->exists()) {
            return;
        }

        DB::table('cms_pages')->insert([
            'slug' => 'projects',
            'name' => 'Projects',
            'heading' => 'Our Projects',
            'content' => '<p>Discover Etihad Group\'s portfolio of residential and commercial projects across Pakistan. From master-planned communities to premium developments, we deliver quality and value.</p><p>Browse our active projects, explore amenities and locations, and find the right investment or home for you.</p>',
            'meta_title' => 'Our Projects | Etihad Group Real Estate',
            'meta_description' => 'Explore Etihad Group real estate projects in Pakistan. Residential and commercial developments with quality construction and prime locations.',
            'meta_keywords' => 'Etihad Group projects, real estate projects Pakistan, housing developments',
            'canonical_url' => null,
            'banner_image' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('cms_pages')->where('slug', 'projects')->delete();
    }
};
