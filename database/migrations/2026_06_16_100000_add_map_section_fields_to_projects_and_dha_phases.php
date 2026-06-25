<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('map_section_heading')->nullable()->after('invest_image');
            $table->string('map_section_tagline', 500)->nullable()->after('map_section_heading');
            $table->string('map_section_image')->nullable()->after('map_section_tagline');
            $table->string('map_section_url', 2000)->nullable()->after('map_section_image');
        });

        Schema::table('dha_phases', function (Blueprint $table) {
            $table->string('map_section_heading')->nullable()->after('show_map_button');
            $table->string('map_section_tagline', 500)->nullable()->after('map_section_heading');
            $table->string('map_section_image')->nullable()->after('map_section_tagline');
            $table->string('map_section_url', 2000)->nullable()->after('map_section_image');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'map_section_heading',
                'map_section_tagline',
                'map_section_image',
                'map_section_url',
            ]);
        });

        Schema::table('dha_phases', function (Blueprint $table) {
            $table->dropColumn([
                'map_section_heading',
                'map_section_tagline',
                'map_section_image',
                'map_section_url',
            ]);
        });
    }
};
