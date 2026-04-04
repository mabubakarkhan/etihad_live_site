<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('visitor_daily_counts')) {
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
            return;
        }

        Schema::table('visitor_daily_counts', function (Blueprint $table) {
            if (!Schema::hasColumn('visitor_daily_counts', 'count_own_listing')) {
                $table->unsignedInteger('count_own_listing')->default(0)->after('count');
            }
            if (!Schema::hasColumn('visitor_daily_counts', 'count_dealer_listing')) {
                $table->unsignedInteger('count_dealer_listing')->default(0)->after('count_own_listing');
            }
            if (!Schema::hasColumn('visitor_daily_counts', 'count_projects')) {
                $table->unsignedInteger('count_projects')->default(0)->after('count_dealer_listing');
            }
        });

        $rows = DB::table('visitor_daily_counts')->where('date', '>=', now()->subDays(6)->format('Y-m-d'))->get();
        $variations = [[5, 4, 5], [7, 5, 7], [4, 3, 4], [9, 6, 8], [6, 4, 6], [4, 3, 5], [8, 5, 7]];
        foreach ($rows as $idx => $row) {
            $v = $variations[$idx % 7] ?? [5, 4, 5];
            DB::table('visitor_daily_counts')->where('id', $row->id)->update([
                'count_own_listing' => $v[0],
                'count_dealer_listing' => $v[1],
                'count_projects' => $v[2],
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('visitor_daily_counts') && Schema::hasColumn('visitor_daily_counts', 'count_own_listing')) {
            Schema::table('visitor_daily_counts', function (Blueprint $table) {
                $table->dropColumn(['count_own_listing', 'count_dealer_listing', 'count_projects']);
            });
        }
    }
};
