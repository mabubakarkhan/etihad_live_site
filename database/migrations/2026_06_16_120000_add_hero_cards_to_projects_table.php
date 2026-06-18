<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (! Schema::hasColumn('projects', 'hero_feature_cards')) {
                $table->json('hero_feature_cards')->nullable()->after('homepage_listing_image');
            }
            if (! Schema::hasColumn('projects', 'hero_stat_cards')) {
                $table->json('hero_stat_cards')->nullable()->after('hero_feature_cards');
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'hero_stat_cards')) {
                $table->dropColumn('hero_stat_cards');
            }
            if (Schema::hasColumn('projects', 'hero_feature_cards')) {
                $table->dropColumn('hero_feature_cards');
            }
        });
    }
};
