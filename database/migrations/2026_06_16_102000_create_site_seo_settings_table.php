<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_seo_settings', function (Blueprint $table) {
            $table->id();
            $table->string('google_analytics_id', 40)->nullable();
            $table->string('google_tag_manager_id', 40)->nullable();
            $table->string('google_ads_id', 40)->nullable();
            $table->string('facebook_pixel_id', 40)->nullable();
            $table->string('tiktok_pixel_id', 40)->nullable();
            $table->string('linkedin_partner_id', 40)->nullable();
            $table->string('hotjar_id', 40)->nullable();
            $table->string('google_site_verification', 120)->nullable();
            $table->string('bing_site_verification', 120)->nullable();
            $table->string('facebook_domain_verification', 120)->nullable();
            $table->string('default_og_image')->nullable();
            $table->text('custom_head_code')->nullable();
            $table->text('custom_body_open_code')->nullable();
            $table->text('custom_body_close_code')->nullable();
            $table->timestamps();
        });

        DB::table('site_seo_settings')->insert([
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('site_seo_settings');
    }
};
