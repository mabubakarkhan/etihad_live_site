<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('properties', 'view_count')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->unsignedInteger('view_count')->default(0)->after('canonical_url');
            });
        }
        if (!Schema::hasColumn('projects', 'view_count')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->unsignedInteger('view_count')->default(0)->after('canonical_url');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('properties', 'view_count')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->dropColumn('view_count');
            });
        }
        if (Schema::hasColumn('projects', 'view_count')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropColumn('view_count');
            });
        }
    }
};
