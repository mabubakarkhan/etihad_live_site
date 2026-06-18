<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dha_phases', function (Blueprint $table) {
            $table->string('phase_pdf')->nullable()->after('card_image');
            $table->string('vr_tour_url', 2000)->nullable()->after('phase_pdf');
            $table->boolean('show_map_button')->default(false)->after('vr_tour_url');
        });
    }

    public function down(): void
    {
        Schema::table('dha_phases', function (Blueprint $table) {
            $table->dropColumn(['phase_pdf', 'vr_tour_url', 'show_map_button']);
        });
    }
};
