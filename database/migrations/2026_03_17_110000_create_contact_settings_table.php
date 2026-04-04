<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_settings', function (Blueprint $table) {
            $table->id();
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('youtube')->nullable();
            $table->timestamps();
        });

        // Insert single row with demo/default values
        DB::table('contact_settings')->insert([
            'address' => '123 Main Boulevard, Lahore, Punjab, Pakistan',
            'latitude' => 31.5204,
            'longitude' => 74.3587,
            'email' => 'info@etihadgroup.com',
            'phone' => '+92 42 123 4567',
            'whatsapp' => '+92 300 1234567',
            'facebook' => 'https://facebook.com/etihadgroup',
            'instagram' => 'https://instagram.com/etihadgroup',
            'linkedin' => 'https://linkedin.com/company/etihadgroup',
            'youtube' => 'https://youtube.com/@etihadgroup',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_settings');
    }
};
