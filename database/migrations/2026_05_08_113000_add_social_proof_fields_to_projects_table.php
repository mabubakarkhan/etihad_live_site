<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->json('testimonial_items')->nullable()->after('pricing_place_cards');
            $table->string('invest_title')->nullable()->after('testimonial_items');
            $table->json('invest_points')->nullable()->after('invest_title');
            $table->string('invest_image')->nullable()->after('invest_points');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['testimonial_items', 'invest_title', 'invest_points', 'invest_image']);
        });
    }
};
