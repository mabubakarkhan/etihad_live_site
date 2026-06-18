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
            $table->string('phases_heading_eyebrow')->nullable()->after('hero_stats');
            $table->string('phases_heading_gold')->nullable()->after('phases_heading_eyebrow');
            $table->string('phases_heading_white')->nullable()->after('phases_heading_gold');
            $table->string('view_all_label')->nullable()->after('phases_heading_white');
            $table->string('view_all_url')->nullable()->after('view_all_label');
            $table->string('why_choose_heading')->nullable()->after('view_all_url');
            $table->json('why_choose_items')->nullable()->after('why_choose_heading');
            $table->string('lifestyle_eyebrow')->nullable()->after('why_choose_items');
            $table->string('lifestyle_heading')->nullable()->after('lifestyle_eyebrow');
            $table->text('lifestyle_description')->nullable()->after('lifestyle_heading');
            $table->string('lifestyle_btn_label')->nullable()->after('lifestyle_description');
            $table->string('lifestyle_btn_url')->nullable()->after('lifestyle_btn_label');
            $table->json('lifestyle_cards')->nullable()->after('lifestyle_btn_url');
            $table->string('growth_heading')->nullable()->after('lifestyle_cards');
            $table->json('growth_stats')->nullable()->after('growth_heading');
            $table->string('cta_banner_image')->nullable()->after('growth_stats');
            $table->string('cta_title_gold')->nullable()->after('cta_banner_image');
            $table->string('cta_title_white')->nullable()->after('cta_title_gold');
            $table->text('cta_description')->nullable()->after('cta_title_white');
            $table->string('cta_btn_primary_label')->nullable()->after('cta_description');
            $table->string('cta_btn_primary_url')->nullable()->after('cta_btn_primary_label');
            $table->string('cta_btn_secondary_label')->nullable()->after('cta_btn_primary_url');
            $table->string('cta_btn_secondary_url')->nullable()->after('cta_btn_secondary_label');
        });

        DB::table('dha_settings')->update([
            'phases_heading_eyebrow' => 'EXPLORE',
            'phases_heading_gold' => 'DHA',
            'phases_heading_white' => 'PHASES',
            'view_all_label' => 'VIEW ALL PROPERTIES',
            'view_all_url' => '/listing',
            'why_choose_heading' => 'WHY CHOOSE DHA LAHORE?',
            'why_choose_items' => json_encode([
                ['icon' => 'map-pin', 'title' => 'Prime Location', 'text' => 'Strategically located in the heart of Lahore with excellent connectivity'],
                ['icon' => 'shield-check', 'title' => 'Secure Living', 'text' => '24/7 security with gated communities and surveillance'],
                ['icon' => 'trending-up', 'title' => 'High ROI', 'text' => 'Consistent property appreciation and rental yields'],
                ['icon' => 'building-2', 'title' => 'Modern Infrastructure', 'text' => 'World-class roads, utilities, and urban planning'],
                ['icon' => 'graduation-cap', 'title' => 'Top Schools', 'text' => 'Access to premier educational institutions'],
                ['icon' => 'heart-pulse', 'title' => 'Healthcare', 'text' => 'Nearby hospitals and medical facilities'],
            ]),
            'lifestyle_eyebrow' => 'A LIFESTYLE',
            'lifestyle_heading' => 'BEYOND EXCELLENCE',
            'lifestyle_description' => 'Experience a lifestyle that combines luxury, convenience, and community. From world-class amenities to serene green spaces, DHA Lahore offers an unparalleled living experience.',
            'lifestyle_btn_label' => 'DISCOVER MORE',
            'lifestyle_btn_url' => '#dha-phases',
            'lifestyle_cards' => json_encode([
                ['label' => 'PARKS & GREEN AREAS', 'image' => ''],
                ['label' => 'GRAND MOSQUES', 'image' => ''],
                ['label' => 'SPORTS COMPLEX', 'image' => ''],
                ['label' => 'COMMERCIAL HUBS', 'image' => ''],
                ['label' => 'FINE DINING', 'image' => ''],
                ['label' => 'CLUB HOUSES', 'image' => ''],
            ]),
            'growth_heading' => 'STRONG TODAY, STRONGER TOMORROW',
            'growth_stats' => json_encode([
                ['icon' => 'trending-up', 'value' => '15-20%', 'label' => 'Average Annual ROI'],
                ['icon' => 'building', 'value' => '100%', 'label' => 'Developed Phases'],
                ['icon' => 'users', 'value' => '50,000+', 'label' => 'Happy Families'],
                ['icon' => 'award', 'value' => 'Premium', 'label' => 'Living Standard'],
                ['icon' => 'globe', 'value' => 'Global', 'label' => 'Recognition'],
            ]),
            'cta_title_gold' => 'YOUR FUTURE',
            'cta_title_white' => 'STARTS HERE',
            'cta_description' => "Join thousands of families who have made DHA Lahore their home.\nExperience the pinnacle of modern living in Pakistan's most prestigious community.",
            'cta_btn_primary_label' => 'EXPLORE PROJECTS',
            'cta_btn_primary_url' => '#dha-phases',
            'cta_btn_secondary_label' => 'BOOK A SITE VISIT',
            'cta_btn_secondary_url' => '/contact-us',
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('dha_settings', function (Blueprint $table) {
            $table->dropColumn([
                'phases_heading_eyebrow',
                'phases_heading_gold',
                'phases_heading_white',
                'view_all_label',
                'view_all_url',
                'why_choose_heading',
                'why_choose_items',
                'lifestyle_eyebrow',
                'lifestyle_heading',
                'lifestyle_description',
                'lifestyle_btn_label',
                'lifestyle_btn_url',
                'lifestyle_cards',
                'growth_heading',
                'growth_stats',
                'cta_banner_image',
                'cta_title_gold',
                'cta_title_white',
                'cta_description',
                'cta_btn_primary_label',
                'cta_btn_primary_url',
                'cta_btn_secondary_label',
                'cta_btn_secondary_url',
            ]);
        });
    }
};
