<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('homepage_why_settings')) {
            Schema::create('homepage_why_settings', function (Blueprint $table) {
                $table->id();
                $table->string('heading_line_1')->nullable();
                $table->string('heading_line_2')->nullable();
                $table->text('description')->nullable();
                $table->string('scroll_label')->nullable();
                $table->string('image_left')->nullable();
                $table->string('image_center')->nullable();
                $table->string('image_right')->nullable();
                $table->string('image_center_back')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('homepage_why_settings') || DB::table('homepage_why_settings')->exists()) {
            return;
        }

        DB::table('homepage_why_settings')->insert([
            'heading_line_1' => 'WHY CHOOSE',
            'heading_line_2' => 'ETIHAD?',
            'description' => 'To achieve flawless interior design from planning to execution, you need a skilled real estate and property development consultant in Pakistan. Our experienced team delivers customized solutions, prioritizing client satisfaction and handling projects of all sizes with precision.',
            'scroll_label' => 'SCROLL',
            'image_left' => null,
            'image_center' => null,
            'image_right' => null,
            'image_center_back' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_why_settings');
    }
};
