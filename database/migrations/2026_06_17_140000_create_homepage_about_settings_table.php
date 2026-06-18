<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('homepage_about_settings')) {
            Schema::create('homepage_about_settings', function (Blueprint $table) {
                $table->id();
                $table->string('tagline_about')->nullable();
                $table->string('tagline_vision')->nullable();
                $table->string('heading_line_1')->nullable();
                $table->string('heading_line_2')->nullable();
                $table->string('video')->nullable();
                $table->text('media_caption_1')->nullable();
                $table->text('media_caption_2')->nullable();
                $table->text('about_para_1_lead')->nullable();
                $table->text('about_para_1_highlight')->nullable();
                $table->text('about_para_2_lead')->nullable();
                $table->text('about_para_2_highlight')->nullable();
                $table->text('vision_para_1_highlight')->nullable();
                $table->text('vision_para_1_body')->nullable();
                $table->text('vision_para_2_lead')->nullable();
                $table->text('vision_para_2_highlight')->nullable();
                $table->text('vision_para_2_body')->nullable();
                $table->string('center_image')->nullable();
                $table->string('secondary_image')->nullable();
                $table->string('cta_text')->nullable();
                $table->string('cta_url')->nullable();
                $table->string('affiliated_text')->nullable();
                $table->string('affiliated_url')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('homepage_about_settings') || DB::table('homepage_about_settings')->exists()) {
            return;
        }

        DB::table('homepage_about_settings')->insert([
            'tagline_about' => 'ABOUT ETIHAD',
            'tagline_vision' => 'OUR VISION',
            'heading_line_1' => 'REFLECT THE SPIRIT',
            'heading_line_2' => 'OF INNOVATION',
            'video' => null,
            'media_caption_1' => 'Enhancing lifestyles through exceptional interior and exterior design.',
            'media_caption_2' => 'Today, we stand tall knowing that our journey has been and is worth it!',
            'about_para_1_lead' => 'Etihad is an established and well-renowned Renovation & fit-out company ',
            'about_para_1_highlight' => 'in Pakistan. Also known as one of the leading Fast Track Projects Service Providers in the Pakistan.',
            'about_para_2_lead' => 'Etihad has only expanded in terms of projects, experience, distinctive solutions, and an eye for aesthetics in its ',
            'about_para_2_highlight' => '13 years in the sector. We believe in bringing visions to life.',
            'vision_para_1_highlight' => 'We redefine ',
            'vision_para_1_body' => 'the urban skyline with architectural designs that merge elegance, innovation, and functionality.',
            'vision_para_2_lead' => 'Our vision is to create ',
            'vision_para_2_highlight' => 'iconic spaces that transcend time,',
            'vision_para_2_body' => ' reflecting the sophisticated and avant-garde spirit of Etihad living.',
            'center_image' => null,
            'secondary_image' => null,
            'cta_text' => 'Learn more',
            'cta_url' => 'javascript:void(0);',
            'affiliated_text' => 'Affiliated pages',
            'affiliated_url' => 'javascript://',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_about_settings');
    }
};
