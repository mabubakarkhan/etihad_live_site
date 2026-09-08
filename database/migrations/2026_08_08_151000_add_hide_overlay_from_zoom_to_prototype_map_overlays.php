<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('prototype_map_overlays') && ! Schema::hasColumn('prototype_map_overlays', 'hide_overlay_from_zoom')) {
            Schema::table('prototype_map_overlays', function (Blueprint $table) {
                $table->unsignedTinyInteger('hide_overlay_from_zoom')->nullable()->after('show_overlay_from_zoom');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('prototype_map_overlays') && Schema::hasColumn('prototype_map_overlays', 'hide_overlay_from_zoom')) {
            Schema::table('prototype_map_overlays', function (Blueprint $table) {
                $table->dropColumn('hide_overlay_from_zoom');
            });
        }
    }
};
