<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('homepage_location_section_settings')) {
            Schema::create('homepage_location_section_settings', function (Blueprint $table) {
                $table->id();
                $table->string('map_background_image')->nullable();
                $table->string('card_image')->nullable();
                $table->string('pin_image')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_location_section_settings');
    }
};
