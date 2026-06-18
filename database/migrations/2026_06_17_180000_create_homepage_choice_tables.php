<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('homepage_choice_settings')) {
            Schema::create('homepage_choice_settings', function (Blueprint $table) {
                $table->id();
                $table->string('section_heading')->nullable();
                $table->string('scroll_label_desktop')->nullable();
                $table->string('scroll_label_mobile')->nullable();
                $table->string('background_image')->nullable();
                $table->string('background_image_portrait')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('homepage_choice_slides')) {
            Schema::create('homepage_choice_slides', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('sort_order')->default(0);
                $table->string('heading_text');
                $table->unsignedInteger('counter_to')->default(0);
                $table->string('counter_text');
                $table->string('description');
                $table->timestamps();
            });
        }

        if (Schema::hasTable('homepage_choice_settings') && ! DB::table('homepage_choice_settings')->exists()) {
            $backgroundPath = null;

            DB::table('homepage_choice_settings')->insert([
                'section_heading' => 'MAKE YOUR CHOICE',
                'scroll_label_desktop' => 'scroll',
                'scroll_label_mobile' => 'drag',
                'background_image' => $backgroundPath,
                'background_image_portrait' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (! Schema::hasTable('homepage_choice_slides') || DB::table('homepage_choice_slides')->exists()) {
            return;
        }

        $slides = [
            ['sort_order' => 1, 'heading_text' => 'Sold Projects', 'counter_to' => 10, 'counter_text' => '10+ ', 'description' => 'successful projects'],
            ['sort_order' => 2, 'heading_text' => 'Valuable Customers', 'counter_to' => 5000, 'counter_text' => '5000+ ', 'description' => 'satisfied customers'],
            ['sort_order' => 3, 'heading_text' => 'Current Project', 'counter_to' => 20, 'counter_text' => '20+ ', 'description' => ' projects in development'],
        ];

        $now = now();
        foreach ($slides as $slide) {
            DB::table('homepage_choice_slides')->insert([
                'sort_order' => $slide['sort_order'],
                'heading_text' => $slide['heading_text'],
                'counter_to' => $slide['counter_to'],
                'counter_text' => $slide['counter_text'],
                'description' => $slide['description'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_choice_slides');
        Schema::dropIfExists('homepage_choice_settings');
    }
};
