<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('projects', 'sort_order')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->unsignedInteger('sort_order')->default(0)->after('id');
            });
        }

        if (! Schema::hasColumn('properties', 'sort_order')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->unsignedInteger('sort_order')->default(0)->after('id');
            });
        }

        DB::table('projects')->orderBy('id')->chunkById(200, function ($rows) {
            foreach ($rows as $row) {
                if ((int) ($row->sort_order ?? 0) === 0) {
                    DB::table('projects')->where('id', $row->id)->update(['sort_order' => $row->id]);
                }
            }
        });

        DB::table('properties')->orderBy('id')->chunkById(200, function ($rows) {
            foreach ($rows as $row) {
                if ((int) ($row->sort_order ?? 0) === 0) {
                    DB::table('properties')->where('id', $row->id)->update(['sort_order' => $row->id]);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
