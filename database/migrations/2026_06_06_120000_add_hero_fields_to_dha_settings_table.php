<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dha_settings', function (Blueprint $table) {
            $table->string('hero_eyebrow')->nullable()->after('heading');
            $table->string('hero_title_gold')->nullable()->after('hero_eyebrow');
            $table->string('hero_title_white')->nullable()->after('hero_title_gold');
            $table->string('hero_subtitle')->nullable()->after('hero_title_white');
            $table->text('hero_description')->nullable()->after('hero_subtitle');
            $table->string('hero_btn_primary_label')->nullable()->after('hero_description');
            $table->string('hero_btn_primary_url')->nullable()->after('hero_btn_primary_label');
            $table->string('hero_btn_secondary_label')->nullable()->after('hero_btn_primary_url');
            $table->string('hero_btn_secondary_url')->nullable()->after('hero_btn_secondary_label');
            $table->json('hero_stats')->nullable()->after('hero_btn_secondary_url');
        });

        $defaultStats = json_encode([
            ['icon' => 'users', 'value' => '54,541+', 'label' => 'Total Plots'],
            ['icon' => 'map', 'value' => '9', 'label' => 'Phases'],
            ['icon' => 'shield-check', 'value' => '100%', 'label' => 'Secure Community'],
            ['icon' => 'tree-pine', 'value' => '25+', 'label' => 'Parks & Green Areas'],
            ['icon' => 'building-2', 'value' => '10+', 'label' => 'Mosques'],
        ]);

        DB::table('dha_settings')->update([
            'hero_eyebrow' => 'WELCOME TO',
            'hero_title_gold' => 'DHA',
            'hero_title_white' => 'LAHORE',
            'hero_subtitle' => "Pakistan's Most Prestigious Residential Community",
            'hero_description' => "Discover Pakistan's most sought-after residential and commercial destination. Experience world-class living with unmatched amenities, security, and investment opportunities in the heart of Lahore.",
            'hero_btn_primary_label' => 'EXPLORE PROJECTS',
            'hero_btn_primary_url' => '#dha-phases',
            'hero_btn_secondary_label' => 'VIEW MASTER PLAN',
            'hero_btn_secondary_url' => '#',
            'hero_stats' => $defaultStats,
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('dha_settings', function (Blueprint $table) {
            $table->dropColumn([
                'hero_eyebrow',
                'hero_title_gold',
                'hero_title_white',
                'hero_subtitle',
                'hero_description',
                'hero_btn_primary_label',
                'hero_btn_primary_url',
                'hero_btn_secondary_label',
                'hero_btn_secondary_url',
                'hero_stats',
            ]);
        });
    }
};
