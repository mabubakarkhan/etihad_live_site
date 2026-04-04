<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->foreignId('project_type_id')->nullable()->constrained('project_types')->nullOnDelete();
            $table->unsignedBigInteger('dealer_id')->default(0)->index(); // 0 = own listing
            $table->text('description')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('address')->nullable();
            $table->string('short_address')->nullable();
            $table->string('town')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('google_map')->nullable();
            $table->string('price_string')->nullable();
            $table->decimal('price_digits', 15, 2)->nullable();
            $table->string('property_type')->nullable(); // plot, home, plaza, flat, apartment, file
            $table->unsignedTinyInteger('bedrooms')->nullable();
            $table->unsignedTinyInteger('bathrooms')->nullable();
            $table->unsignedTinyInteger('garage')->nullable();
            $table->unsignedTinyInteger('kitchen')->nullable();
            $table->decimal('area_marla', 10, 2)->nullable();
            $table->decimal('area_kanal', 10, 2)->nullable();
            $table->text('amenities_description')->nullable();
            $table->json('videos')->nullable(); // main video embed codes
            $table->json('gallery')->nullable(); // images [{path, order}]
            $table->json('video_gallery')->nullable(); // embed codes
            $table->json('features')->nullable(); // [title, ...]
            $table->json('location_accessibility')->nullable();
            $table->json('nearest_hospitals')->nullable();
            $table->json('nearest_markets')->nullable();
            $table->json('nearest_restaurants')->nullable();
            $table->json('amenities')->nullable(); // [{icon, title}, ...]
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
