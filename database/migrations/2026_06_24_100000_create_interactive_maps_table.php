<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('interactive_maps')) {
            return;
        }

        Schema::create('interactive_maps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained('projects')->cascadeOnDelete();
            $table->foreignId('dha_phase_id')->nullable()->constrained('dha_phases')->cascadeOnDelete();
            $table->string('overlay_image_path')->nullable();
            $table->decimal('north', 10, 7)->nullable();
            $table->decimal('south', 10, 7)->nullable();
            $table->decimal('east', 10, 7)->nullable();
            $table->decimal('west', 10, 7)->nullable();
            $table->unsignedTinyInteger('default_zoom')->default(15);
            $table->unsignedTinyInteger('min_zoom')->default(10);
            $table->unsignedTinyInteger('max_zoom')->default(20);
            $table->decimal('overlay_opacity', 3, 2)->default(0.85);
            $table->decimal('overlay_rotation', 8, 2)->default(0);
            $table->unsignedTinyInteger('overlay_visibility_zoom')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('project_id');
            $table->unique('dha_phase_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interactive_maps');
    }
};
