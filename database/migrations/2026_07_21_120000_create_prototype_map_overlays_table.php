<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prototype_map_overlays', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('overlay_image')->nullable();
            $table->decimal('north', 11, 8)->nullable();
            $table->decimal('south', 11, 8)->nullable();
            $table->decimal('east', 11, 8)->nullable();
            $table->decimal('west', 11, 8)->nullable();
            $table->unsignedTinyInteger('default_zoom')->default(15);
            $table->unsignedTinyInteger('min_zoom')->default(10);
            $table->unsignedTinyInteger('max_zoom')->default(20);
            $table->decimal('overlay_opacity', 4, 2)->default(0.85);
            $table->decimal('overlay_rotation', 6, 2)->default(0);
            $table->unsignedTinyInteger('show_overlay_from_zoom')->nullable();
            $table->string('status', 20)->default('draft');
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prototype_map_overlays');
    }
};
