<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homepage_dealers_section_settings', function (Blueprint $table) {
            $table->id();
            $table->string('eyebrow')->nullable();
            $table->string('title_line_1')->nullable();
            $table->string('title_highlight')->nullable();
            $table->text('description')->nullable();
            $table->string('footer_note')->nullable();
            $table->string('card_badge')->nullable();
            $table->string('cta_label')->nullable();
            $table->string('view_all_label')->nullable();
            $table->timestamps();
        });

        DB::table('homepage_dealers_section_settings')->insert([
            'eyebrow' => 'Trusted Agents',
            'title_line_1' => 'Explore Our',
            'title_highlight' => 'Popular Agents',
            'description' => 'Meet verified Etihad dealers with active listings across DHA Lahore — browse profiles, property counts, and connect directly with the right agent.',
            'footer_note' => 'Scroll through featured agents on Etihad',
            'card_badge' => 'Trusted Agent',
            'cta_label' => 'View profile',
            'view_all_label' => 'View All Agents',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_dealers_section_settings');
    }
};
