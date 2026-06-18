<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('property_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('property_requests', 'property_type')) {
                $table->string('property_type', 120)->nullable()->after('email');
            }
            if (! Schema::hasColumn('property_requests', 'budget')) {
                $table->string('budget', 120)->nullable()->after('property_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('property_requests', function (Blueprint $table) {
            if (Schema::hasColumn('property_requests', 'budget')) {
                $table->dropColumn('budget');
            }
            if (Schema::hasColumn('property_requests', 'property_type')) {
                $table->dropColumn('property_type');
            }
        });
    }
};
