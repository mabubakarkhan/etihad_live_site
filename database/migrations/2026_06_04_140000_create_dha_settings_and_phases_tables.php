<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dha_settings', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('DHA Lahore');
            $table->string('slug')->default('dha');
            $table->string('heading')->nullable();
            $table->longText('content')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('dha_phases', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->unsignedInteger('sort_order')->default(0);
            $table->longText('description')->nullable();
            $table->string('featured_image')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->unsignedTinyInteger('map_zoom')->default(14);
            $table->text('google_map')->nullable();
            $table->json('image_gallery')->nullable();
            $table->json('video_gallery')->nullable();
            $table->json('plot_maps')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('dha_phase_project_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dha_phase_id')->constrained('dha_phases')->cascadeOnDelete();
            $table->foreignId('project_type_id')->constrained('project_types')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['dha_phase_id', 'project_type_id']);
        });

        DB::table('dha_settings')->insert([
            'title' => 'DHA Lahore',
            'slug' => 'dha',
            'heading' => 'DHA Lahore — Defence Housing Authority',
            'content' => '<p>Explore DHA Lahore phases, plot maps, and verified dealer listings across residential, commercial, and plaza opportunities.</p>',
            'meta_title' => 'DHA Lahore Phases | Etihad Marketing',
            'meta_description' => 'Browse DHA Lahore phases with maps, plot plans, galleries, and dealer property listings.',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $now = now();
        for ($i = 1; $i <= 11; $i++) {
            DB::table('dha_phases')->insert([
                'title' => 'DHA Phase ' . $i,
                'slug' => 'dha-phase-' . $i,
                'sort_order' => $i,
                'description' => '<p>Details for DHA Phase ' . $i . ' — update content, maps, and galleries in admin.</p>',
                'meta_title' => 'DHA Phase ' . $i . ' Lahore | Etihad Marketing',
                'meta_description' => 'Explore DHA Phase ' . $i . ' Lahore — maps, plot plans, property types, and dealer listings.',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('dha_phase_project_type');
        Schema::dropIfExists('dha_phases');
        Schema::dropIfExists('dha_settings');
    }
};
