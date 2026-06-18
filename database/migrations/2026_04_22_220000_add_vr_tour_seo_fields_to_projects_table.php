<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'vr_tour_meta_title')) {
                $table->string('vr_tour_meta_title')->nullable()->after('vr_tour_url');
            }
            if (!Schema::hasColumn('projects', 'vr_tour_meta_description')) {
                $table->string('vr_tour_meta_description', 500)->nullable()->after('vr_tour_meta_title');
            }
            if (!Schema::hasColumn('projects', 'vr_tour_meta_keywords')) {
                $table->string('vr_tour_meta_keywords', 500)->nullable()->after('vr_tour_meta_description');
            }
            if (!Schema::hasColumn('projects', 'vr_tour_canonical_url')) {
                $table->string('vr_tour_canonical_url', 500)->nullable()->after('vr_tour_meta_keywords');
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $dropColumns = [];
            foreach (['vr_tour_meta_title', 'vr_tour_meta_description', 'vr_tour_meta_keywords', 'vr_tour_canonical_url'] as $column) {
                if (Schema::hasColumn('projects', $column)) {
                    $dropColumns[] = $column;
                }
            }
            if (!empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
