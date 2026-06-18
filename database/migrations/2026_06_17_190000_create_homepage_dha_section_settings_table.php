<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('homepage_dha_section_settings')) {
            Schema::create('homepage_dha_section_settings', function (Blueprint $table) {
                $table->id();
                $table->string('eyebrow')->nullable();
                $table->string('title_line_1')->nullable();
                $table->string('title_highlight')->nullable();
                $table->text('description')->nullable();
                $table->string('footer_note')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('homepage_dha_section_settings') || DB::table('homepage_dha_section_settings')->exists()) {
            return;
        }

        DB::table('homepage_dha_section_settings')->insert([
            'eyebrow' => 'Defence Housing Authority',
            'title_line_1' => 'Discover',
            'title_highlight' => 'DHA Phases',
            'description' => 'Explore every active DHA phase across Lahore — master-planned communities with premium plots, modern infrastructure, and strong long-term investment potential.',
            'footer_note' => 'Scroll through all DHA phases on Etihad',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_dha_section_settings');
    }
};
