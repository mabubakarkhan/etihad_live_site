<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('first_visit_popup_settings', 'force_show_every_time')) {
            Schema::table('first_visit_popup_settings', function (Blueprint $table) {
                $table->boolean('force_show_every_time')->default(false)->after('is_enabled');
            });
        }

        // Enable testing mode for now so popup shows on every load
        DB::table('first_visit_popup_settings')->update([
            'force_show_every_time' => true,
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('first_visit_popup_settings', 'force_show_every_time')) {
            Schema::table('first_visit_popup_settings', function (Blueprint $table) {
                $table->dropColumn('force_show_every_time');
            });
        }
    }
};
