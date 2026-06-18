<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homepage_achievements_settings', function (Blueprint $table) {
            $table->id();
            $table->string('title_line_1')->nullable();
            $table->string('title_highlight')->nullable();
            $table->timestamps();
        });

        Schema::create('homepage_achievement_stats', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('value');
            $table->string('suffix')->nullable();
            $table->string('label');
            $table->timestamps();
        });

        DB::table('homepage_achievements_settings')->insert([
            'title_line_1' => 'Our',
            'title_highlight' => 'Achievements',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $stats = [
            ['sort_order' => 1, 'value' => '5000', 'suffix' => '+', 'label' => 'Satisfied Customers'],
            ['sort_order' => 2, 'value' => '10', 'suffix' => '+', 'label' => 'Completed Projects'],
            ['sort_order' => 3, 'value' => '20', 'suffix' => '+', 'label' => 'Projects in Development'],
            ['sort_order' => 4, 'value' => '15', 'suffix' => null, 'label' => 'Years of Experience'],
            ['sort_order' => 5, 'value' => '100', 'suffix' => '%', 'label' => 'Customer Satisfaction Rate'],
            ['sort_order' => 6, 'value' => '50', 'suffix' => '+', 'label' => 'Team Members'],
        ];

        $now = now();
        foreach ($stats as $stat) {
            DB::table('homepage_achievement_stats')->insert([
                'sort_order' => $stat['sort_order'],
                'value' => $stat['value'],
                'suffix' => $stat['suffix'],
                'label' => $stat['label'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_achievement_stats');
        Schema::dropIfExists('homepage_achievements_settings');
    }
};
