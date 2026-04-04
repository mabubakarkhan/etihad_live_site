<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_type_id')->nullable()->constrained('project_types')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('price')->nullable();
            $table->text('description')->nullable();

            // Address
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('short_address')->nullable();
            $table->text('full_address')->nullable();
            $table->text('google_map')->nullable();
            $table->string('address_image')->nullable();

            // Media
            $table->string('logo')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('homepage_listing_image')->nullable();

            // Featured video
            $table->string('featured_youtube_url')->nullable();
            $table->string('featured_video_title')->nullable();
            $table->longText('featured_video_description')->nullable();

            $table->longText('about_developers')->nullable();
            $table->string('project_file_pdf')->nullable();

            // NOC & Planning
            $table->longText('noc_planning_content')->nullable();
            $table->string('noc_planning_image')->nullable();

            // Future note
            $table->string('future_note_title')->nullable();
            $table->longText('future_note_content')->nullable();

            // Extra section (title + rich text)
            $table->string('extra_section_title')->nullable();
            $table->longText('extra_section_content')->nullable();

            // JSON repeatable sections
            $table->json('unique_features')->nullable();           // [{title, icon}, ...]
            $table->string('price_plan_section_title')->nullable();
            $table->json('price_plan_items')->nullable();        // ["string", ...]
            $table->json('faqs')->nullable();                     // [{question, answer}, ...]
            $table->json('plans')->nullable();                     // [{title, image}, ...]
            $table->json('title_descriptions')->nullable();        // {section_title, section_description, items: [{title, description}]}
            $table->json('videos')->nullable();                   // [{url}, ...]
            $table->json('gallery')->nullable();                  // [{path, order}, ...]

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
