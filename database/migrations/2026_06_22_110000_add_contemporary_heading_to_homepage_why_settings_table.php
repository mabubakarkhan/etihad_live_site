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
            return;
        }

        if (! Schema::hasColumn('homepage_why_settings', 'contemporary_heading')) {
            Schema::table('homepage_why_settings', function (Blueprint $table) {
                $table->string('contemporary_heading')->nullable()->after('scroll_label');
            });
        }

        DB::table('homepage_why_settings')
            ->whereNull('contemporary_heading')
            ->orWhere('contemporary_heading', '')
            ->update(['contemporary_heading' => 'CONTEMPORARY']);
    }

    public function down(): void
    {
        if (Schema::hasTable('homepage_why_settings') && Schema::hasColumn('homepage_why_settings', 'contemporary_heading')) {
            Schema::table('homepage_why_settings', function (Blueprint $table) {
                $table->dropColumn('contemporary_heading');
            });
        }
    }
};
