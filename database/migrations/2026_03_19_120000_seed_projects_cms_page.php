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
            'content' => '<p>Explore our featured real estate projects. We offer residential and commercial developments across Pakistan.</p>',
            'meta_title' => 'Our Projects | ' . config('app.name'),
            'meta_description' => 'Browse featured real estate projects from Etihad. Residential and commercial developments across Pakistan.',
            'meta_keywords' => 'projects, real estate, Pakistan, residential, commercial',
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
