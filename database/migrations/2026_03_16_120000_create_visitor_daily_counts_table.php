<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitor_daily_counts', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->unsignedInteger('count')->default(0);
            $table->unsignedInteger('count_own_listing')->default(0);
            $table->unsignedInteger('count_dealer_listing')->default(0);
            $table->unsignedInteger('count_projects')->default(0);
            $table->timestamps();
        });

        $now = now();
        $variations = [
            ['count' => 14, 'own' => 5, 'dealer' => 4, 'projects' => 5],
            ['count' => 19, 'own' => 7, 'dealer' => 5, 'projects' => 7],
            ['count' => 11, 'own' => 4, 'dealer' => 3, 'projects' => 4],
            ['count' => 23, 'own' => 9, 'dealer' => 6, 'projects' => 8],
            ['count' => 16, 'own' => 6, 'dealer' => 4, 'projects' => 6],
            ['count' => 12, 'own' => 4, 'dealer' => 3, 'projects' => 5],
            ['count' => 20, 'own' => 8, 'dealer' => 5, 'projects' => 7],
        ];
        for ($i = 6; $i >= 0; $i--) {
            $day = $now->copy()->subDays($i)->format('Y-m-d');
            $v = $variations[6 - $i];
            DB::table('visitor_daily_counts')->insert([
                'date' => $day,
                'count' => $v['count'],
                'count_own_listing' => $v['own'],
                'count_dealer_listing' => $v['dealer'],
                'count_projects' => $v['projects'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('visitor_daily_counts');
    }
};
