<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('homepage_hero_settings')) {
            Schema::create('homepage_hero_settings', function (Blueprint $table) {
                $table->id();
                $table->string('hero_image')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_hero_settings');
    }
};
