<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('cms_pages')->where('slug', 'our-teams')->exists()) {
            return;
        }
        DB::table('cms_pages')->insert([
            'slug' => 'our-teams',
            'name' => 'Our Teams',
            'heading' => 'Our Team',
            'content' => '<p>Meet our dedicated team of professionals. We work together to deliver the best real estate experience.</p>',
            'meta_title' => 'Our Team | ' . config('app.name'),
            'meta_description' => 'Meet the Etihad Group team. Our dealers and professionals are here to help you find your ideal property.',
            'meta_keywords' => 'our team, dealers, real estate team, Etihad Group',
            'canonical_url' => null,
            'banner_image' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('cms_pages')->where('slug', 'our-teams')->delete();
    }
};
