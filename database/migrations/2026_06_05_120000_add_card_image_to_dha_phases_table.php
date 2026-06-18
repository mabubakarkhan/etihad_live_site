<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dha_phases', function (Blueprint $table) {
            $table->string('card_image')->nullable()->after('featured_image');
        });
    }

    public function down(): void
    {
        Schema::table('dha_phases', function (Blueprint $table) {
            $table->dropColumn('card_image');
        });
    }
};
