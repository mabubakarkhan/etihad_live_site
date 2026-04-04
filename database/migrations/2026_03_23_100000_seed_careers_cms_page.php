<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('cms_pages')->where('slug', 'careers')->exists()) {
            return;
        }
        DB::table('cms_pages')->insert([
            'slug' => 'careers',
            'name' => 'Careers',
            'heading' => 'Careers',
            'content' => '<p>Join our team. Explore open positions and find your next opportunity at Etihad Group.</p>',
            'meta_title' => 'Careers | ' . config('app.name'),
            'meta_description' => 'View career opportunities at Etihad Group. Browse open positions and apply today.',
            'meta_keywords' => 'careers, jobs, Etihad Group, employment',
            'canonical_url' => null,
            'banner_image' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('cms_pages')->where('slug', 'careers')->delete();
    }
};
