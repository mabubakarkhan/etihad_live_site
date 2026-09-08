<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('interactive_maps', function (Blueprint $table) {
            if (! Schema::hasColumn('interactive_maps', 'overlay_path')) {
                $table->json('overlay_path')->nullable()->after('west');
            }
        });
    }

    public function down(): void
    {
        Schema::table('interactive_maps', function (Blueprint $table) {
            if (Schema::hasColumn('interactive_maps', 'overlay_path')) {
                $table->dropColumn('overlay_path');
            }
        });
    }
};
