<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prototype_map_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prototype_map_overlay_id')
                ->constrained('prototype_map_overlays')
                ->cascadeOnDelete();
            $table->string('title');
            $table->string('section_type', 30)->default('polygon');
            $table->json('geometry');
            $table->string('fill_color', 20)->default('#a9823d');
            $table->string('stroke_color', 20)->default('#6c4815');
            $table->decimal('fill_opacity', 4, 2)->default(0.45);
            $table->decimal('stroke_opacity', 4, 2)->default(0.90);
            $table->unsignedSmallInteger('stroke_weight')->default(2);
            $table->string('label', 100)->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('active');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['prototype_map_overlay_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prototype_map_sections');
    }
};
