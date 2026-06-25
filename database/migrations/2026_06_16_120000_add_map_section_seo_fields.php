<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('map_section_meta_title')->nullable()->after('map_section_url');
            $table->string('map_section_meta_description', 500)->nullable()->after('map_section_meta_title');
            $table->string('map_section_meta_keywords', 500)->nullable()->after('map_section_meta_description');
        });

        Schema::table('dha_phases', function (Blueprint $table) {
            $table->string('map_section_meta_title')->nullable()->after('map_section_url');
            $table->string('map_section_meta_description', 500)->nullable()->after('map_section_meta_title');
            $table->string('map_section_meta_keywords', 500)->nullable()->after('map_section_meta_description');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'map_section_meta_title',
                'map_section_meta_description',
                'map_section_meta_keywords',
            ]);
        });

        Schema::table('dha_phases', function (Blueprint $table) {
            $table->dropColumn([
                'map_section_meta_title',
                'map_section_meta_description',
                'map_section_meta_keywords',
            ]);
        });
    }
};
