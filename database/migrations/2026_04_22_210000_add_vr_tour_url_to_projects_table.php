<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'vr_tour_url')) {
                $table->string('vr_tour_url', 2000)->nullable()->after('featured_video_description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'vr_tour_url')) {
                $table->dropColumn('vr_tour_url');
            }
        });
    }
};
