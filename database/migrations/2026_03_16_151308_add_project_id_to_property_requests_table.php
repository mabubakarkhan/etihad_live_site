<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('property_requests', function (Blueprint $table) {
            if (Schema::hasColumn('property_requests', 'property_id')) {
                $table->dropForeign(['property_id']);
            }
            if (!Schema::hasColumn('property_requests', 'project_id')) {
                $table->unsignedBigInteger('project_id')->default(0)->after('property_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_requests', function (Blueprint $table) {
            if (Schema::hasColumn('property_requests', 'project_id')) {
                $table->dropColumn('project_id');
            }
        });
    }
};
