<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('homepage_footer_settings')) {
            Schema::create('homepage_footer_settings', function (Blueprint $table) {
                $table->id();
                $table->string('footer_image')->nullable();
                $table->string('footer_image_alt')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_footer_settings');
    }
};
