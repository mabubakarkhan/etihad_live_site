<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (! Schema::hasColumn('projects', 'tabs_follow_content')) {
                $table->longText('tabs_follow_content')->nullable()->after('project_detail_tabs');
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'tabs_follow_content')) {
                $table->dropColumn('tabs_follow_content');
            }
        });
    }
};
