<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        migration_create_table('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wp_term_id')->nullable()->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        migration_create_table('blog_tags', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wp_term_id')->nullable()->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        migration_create_table('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wp_post_id')->nullable()->unique();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('featured_image_source', 500)->nullable();
            $table->string('status', 20)->default('published');
            $table->timestamp('published_at')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->string('meta_keywords', 500)->nullable();
            $table->string('canonical_url', 500)->nullable();
            $table->string('meta_robots', 120)->nullable();
            $table->string('og_title')->nullable();
            $table->string('og_description', 500)->nullable();
            $table->string('og_image', 500)->nullable();
            $table->string('twitter_title')->nullable();
            $table->string('twitter_description', 500)->nullable();
            $table->string('twitter_card', 80)->nullable();
            $table->timestamps();

            $table->index(['status', 'published_at']);
        });

        migration_create_table('blog_post_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_post_id')->constrained('blog_posts')->cascadeOnDelete();
            $table->foreignId('blog_category_id')->constrained('blog_categories')->cascadeOnDelete();
            $table->unique(['blog_post_id', 'blog_category_id']);
        });

        migration_create_table('blog_post_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_post_id')->constrained('blog_posts')->cascadeOnDelete();
            $table->foreignId('blog_tag_id')->constrained('blog_tags')->cascadeOnDelete();
            $table->unique(['blog_post_id', 'blog_tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_post_tag');
        Schema::dropIfExists('blog_post_category');
        Schema::dropIfExists('blog_posts');
        Schema::dropIfExists('blog_tags');
        Schema::dropIfExists('blog_categories');
    }
};
