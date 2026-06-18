<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('contact_settings', 'website')) {
                $table->string('website', 500)->nullable()->after('tiktok');
            }
            if (! Schema::hasColumn('contact_settings', 'map_url')) {
                $table->string('map_url', 500)->nullable()->after('website');
            }
            if (! Schema::hasColumn('contact_settings', 'office_title')) {
                $table->string('office_title', 255)->nullable()->after('map_url');
            }
            if (! Schema::hasColumn('contact_settings', 'location_image')) {
                $table->string('location_image')->nullable()->after('office_title');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contact_settings', function (Blueprint $table) {
            foreach (['website', 'map_url', 'office_title', 'location_image'] as $column) {
                if (Schema::hasColumn('contact_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
