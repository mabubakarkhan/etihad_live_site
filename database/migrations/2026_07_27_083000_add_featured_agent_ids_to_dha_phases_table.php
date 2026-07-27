<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('dha_phases', 'featured_agent_ids')) {
            Schema::table('dha_phases', function (Blueprint $table) {
                $table->json('featured_agent_ids')->nullable()->after('nearby_facilities');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('dha_phases', 'featured_agent_ids')) {
            Schema::table('dha_phases', function (Blueprint $table) {
                $table->dropColumn('featured_agent_ids');
            });
        }
    }
};
