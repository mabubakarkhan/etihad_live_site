<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 80)->unique();
            $table->string('name', 120);
            $table->string('heading')->nullable();
            $table->longText('content')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->string('meta_keywords', 500)->nullable();
            $table->string('canonical_url', 500)->nullable();
            $table->string('banner_image')->nullable();
            $table->timestamps();
        });

        $pages = [
            [
                'slug' => 'home',
                'name' => 'Home',
                'heading' => 'Welcome to Etihad Group',
                'content' => '<p>Etihad Group is a leading real estate developer in Pakistan. We deliver quality residential and commercial projects with a commitment to excellence and customer satisfaction.</p><p>Explore our featured projects and property listings to find your ideal investment or dream home.</p>',
                'meta_title' => 'Etihad Group | Premier Real Estate in Pakistan',
                'meta_description' => 'Etihad Group offers premium real estate projects and property listings across Pakistan. Find your dream home or investment opportunity.',
                'meta_keywords' => 'real estate, Pakistan, Etihad Group, property, projects, housing',
                'canonical_url' => null,
                'banner_image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'about-us',
                'name' => 'About Us',
                'heading' => 'About Etihad Group',
                'content' => '<p>Founded in 2004, Etihad Group has established itself as one of Pakistan\'s most trusted real estate developers. Our mission is to lead the land development sector and rank as the country\'s top property developer.</p><p>We are known for exceptional customer care and the delivery of quality projects including renowned developments such as the LUMS Campus. Our team is dedicated to helping clients find their ideal home or investment.</p>',
                'meta_title' => 'About Us | Etihad Group Real Estate',
                'meta_description' => 'Learn about Etihad Group, a leading real estate developer in Pakistan since 2004. Our mission, values, and commitment to quality.',
                'meta_keywords' => 'about Etihad Group, real estate Pakistan, property developer',
                'canonical_url' => null,
                'banner_image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'contact-us',
                'name' => 'Contact Us',
                'heading' => 'Get in Touch',
                'content' => '<p>We would love to hear from you. Whether you have a question about our projects, listings, or need assistance, our team is ready to help.</p><p>Reach out via the details in Contact Settings, or use the form below. We typically respond within 24 hours.</p>',
                'meta_title' => 'Contact Us | Etihad Group',
                'meta_description' => 'Contact Etihad Group for inquiries about real estate projects and property listings. We are here to help.',
                'meta_keywords' => 'contact Etihad Group, real estate inquiry, Pakistan',
                'canonical_url' => null,
                'banner_image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'listing',
                'name' => 'Listing',
                'heading' => 'Property Listings',
                'content' => '<p>Browse our handpicked property listings. We offer a wide range of options including plots, homes, apartments, and commercial spaces to suit every need and budget.</p><p>Use the filters to narrow your search by location, type, and purpose. All listings are verified and updated regularly.</p>',
                'meta_title' => 'Property Listings | Etihad Group',
                'meta_description' => 'Browse property listings from Etihad Group. Plots, homes, apartments, and commercial real estate across Pakistan.',
                'meta_keywords' => 'property listings, real estate, Pakistan, plots, homes',
                'canonical_url' => null,
                'banner_image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'listing-dealers',
                'name' => 'Listing Dealers',
                'heading' => 'Dealer Listings',
                'content' => '<p>Explore properties listed by our verified dealers. These listings offer additional variety and options from trusted partners across the region.</p><p>Each dealer is vetted to ensure quality and reliability. Find your next property from our dealer network.</p>',
                'meta_title' => 'Dealer Listings | Etihad Group',
                'meta_description' => 'Browse dealer property listings on Etihad Group. Verified dealers and quality real estate options.',
                'meta_keywords' => 'dealer listings, property dealers, real estate Pakistan',
                'canonical_url' => null,
                'banner_image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('cms_pages')->insert($pages);
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_pages');
    }
};
