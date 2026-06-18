<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dha_phases', function (Blueprint $table) {
            $table->text('hero_lead')->nullable()->after('description');
            $table->string('stat_location')->nullable()->after('hero_lead');
            $table->string('stat_total_area')->nullable()->after('stat_location');
            $table->string('stat_total_plots')->nullable()->after('stat_total_area');
            $table->string('stat_year_developed')->nullable()->after('stat_total_plots');
            $table->longText('features_content')->nullable()->after('stat_year_developed');
            $table->longText('market_insights')->nullable()->after('features_content');
            $table->text('contact_intro')->nullable()->after('market_insights');
        });

        $defaults = [
            1 => ['area' => '5,987 Kanal', 'plots' => '54,541+', 'year' => '2002'],
            2 => ['area' => '6,200 Kanal', 'plots' => '48,200+', 'year' => '2005'],
            3 => ['area' => '5,450 Kanal', 'plots' => '42,800+', 'year' => '2008'],
            4 => ['area' => '4,980 Kanal', 'plots' => '38,500+', 'year' => '2011'],
            5 => ['area' => '4,650 Kanal', 'plots' => '35,200+', 'year' => '2014'],
            6 => ['area' => '4,200 Kanal', 'plots' => '31,600+', 'year' => '2016'],
            7 => ['area' => '3,850 Kanal', 'plots' => '28,400+', 'year' => '2018'],
            8 => ['area' => '3,500 Kanal', 'plots' => '25,100+', 'year' => '2020'],
            9 => ['area' => '3,200 Kanal', 'plots' => '22,800+', 'year' => '2021'],
            10 => ['area' => '2,900 Kanal', 'plots' => '19,500+', 'year' => '2022'],
            11 => ['area' => '2,600 Kanal', 'plots' => '16,200+', 'year' => '2023'],
        ];

        $phases = DB::table('dha_phases')->orderBy('sort_order')->orderBy('id')->get(['id', 'title', 'sort_order']);
        foreach ($phases as $phase) {
            $n = (int) ($phase->sort_order ?: $phase->id);
            if ($n < 1 || $n > 11) {
                $n = min(max($n, 1), 11);
            }
            $d = $defaults[$n] ?? $defaults[1];
            $title = $phase->title;

            DB::table('dha_phases')->where('id', $phase->id)->update([
                'hero_lead' => 'A perfect blend of prime location, modern infrastructure, and high investment potential.',
                'stat_location' => 'Lahore, Pakistan',
                'stat_total_area' => $d['area'],
                'stat_total_plots' => $d['plots'],
                'stat_year_developed' => $d['year'],
                'features_content' => '<p>' . e($title) . ' offers planned residential and commercial blocks, wide roads, underground utilities, parks, and community facilities designed for modern living.</p><ul><li>Gated community with 24/7 security</li><li>Underground electrification &amp; fiber connectivity</li><li>Parks, mosques, and commercial boulevards</li><li>Direct access to major city arteries</li></ul>',
                'market_insights' => '<p>Property values in ' . e($title) . ' have shown steady appreciation driven by infrastructure completion, commercial activity, and sustained buyer demand from end-users and investors.</p><p>Average plot and home prices remain competitive versus comparable premium societies in Lahore, with strong rental yield in established sectors.</p>',
                'contact_intro' => 'Speak with our DHA specialists for availability, pricing, and verified listings in ' . $title . '.',
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('dha_phases', function (Blueprint $table) {
            $table->dropColumn([
                'hero_lead',
                'stat_location',
                'stat_total_area',
                'stat_total_plots',
                'stat_year_developed',
                'features_content',
                'market_insights',
                'contact_intro',
            ]);
        });
    }
};
