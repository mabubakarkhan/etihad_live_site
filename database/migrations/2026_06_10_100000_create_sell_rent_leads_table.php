<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sell_rent_leads', function (Blueprint $table) {
            $table->id();
            $table->string('intent', 20);
            $table->string('rent_frequency', 20)->nullable();
            $table->string('location');
            $table->string('category', 20);
            $table->string('property_type', 40)->nullable();
            $table->string('bedrooms', 20)->nullable();
            $table->string('area_sqft', 40)->nullable();
            $table->string('furnishing', 40)->nullable();
            $table->string('urgency', 40)->nullable();
            $table->string('name');
            $table->string('phone', 60);
            $table->string('email')->nullable();
            $table->string('status', 20)->default('new');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sell_rent_leads');
    }
};
