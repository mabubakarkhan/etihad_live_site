<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homepage_investment_journey_settings', function (Blueprint $table) {
            $table->id();
            $table->string('title_line_1')->nullable();
            $table->string('title_highlight')->nullable();
            $table->timestamps();
        });

        Schema::create('homepage_investment_journey_steps', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('title');
            $table->text('description');
            $table->timestamps();
        });

        DB::table('homepage_investment_journey_settings')->insert([
            'title_line_1' => 'Real Estate Investment',
            'title_highlight' => 'Journey',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $steps = [
            ['sort_order' => 1, 'title' => '1. Discovery', 'description' => 'Explore our premium properties and identify the perfect investment opportunity that matches your goals and budget.'],
            ['sort_order' => 2, 'title' => '2. Consultation', 'description' => 'Our expert team provides personalized guidance and market insights to help you make informed decisions.'],
            ['sort_order' => 3, 'title' => '3. Documentation', 'description' => 'Complete all legal requirements with our transparent process and comprehensive documentation support.'],
            ['sort_order' => 4, 'title' => '4. Investment', 'description' => 'Secure your investment with flexible payment plans and guaranteed returns on your real estate portfolio.'],
            ['sort_order' => 5, 'title' => '5. Growth', 'description' => 'Watch your investment appreciate with our expert management and strategic property development.'],
        ];

        $now = now();
        foreach ($steps as $step) {
            DB::table('homepage_investment_journey_steps')->insert([
                'sort_order' => $step['sort_order'],
                'title' => $step['title'],
                'description' => $step['description'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_investment_journey_steps');
        Schema::dropIfExists('homepage_investment_journey_settings');
    }
};
