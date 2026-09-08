<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('prototype_map_overlays') && ! Schema::hasColumn('prototype_map_overlays', 'show_label_from_zoom')) {
            Schema::table('prototype_map_overlays', function (Blueprint $table) {
                $table->unsignedTinyInteger('show_label_from_zoom')->nullable()->after('show_overlay_from_zoom');
            });
        }

        if (Schema::hasTable('prototype_map_sections') && ! Schema::hasColumn('prototype_map_sections', 'show_label_from_zoom')) {
            Schema::table('prototype_map_sections', function (Blueprint $table) {
                $table->unsignedTinyInteger('show_label_from_zoom')->nullable()->after('label');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('prototype_map_overlays') && Schema::hasColumn('prototype_map_overlays', 'show_label_from_zoom')) {
            Schema::table('prototype_map_overlays', function (Blueprint $table) {
                $table->dropColumn('show_label_from_zoom');
            });
        }

        if (Schema::hasTable('prototype_map_sections') && Schema::hasColumn('prototype_map_sections', 'show_label_from_zoom')) {
            Schema::table('prototype_map_sections', function (Blueprint $table) {
                $table->dropColumn('show_label_from_zoom');
            });
        }
    }
};
