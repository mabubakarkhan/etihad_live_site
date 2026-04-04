<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('status', 20)->default('active')->after('slug')->index();
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->string('status', 20)->default('active')->after('slug')->index();
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
