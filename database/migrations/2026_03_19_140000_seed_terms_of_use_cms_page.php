<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('cms_pages')->where('slug', 'terms-of-use')->exists()) {
            return;
        }

        $content = '<p>By using this website, you agree to these Terms of Use. Please read them carefully.</p>'
            . '<h3>Use of Website</h3>'
            . '<p>You may use our website for lawful purposes only. You must not use it in any way that breaches applicable laws or that could harm, disable, or impair the site or any user.</p>'
            . '<h3>Property Listings and Information</h3>'
            . '<p>Property and project information on this site is provided for general reference. We do not guarantee its accuracy or completeness. You should verify details and conduct your own due diligence before making any decision.</p>'
            . '<h3>Intellectual Property</h3>'
            . '<p>Content on this website, including text, images, and logos, is owned by us or our licensors. You may not copy, reproduce, or use it without our prior written permission.</p>'
            . '<h3>Limitation of Liability</h3>'
            . '<p>We are not liable for any direct, indirect, or consequential loss arising from your use of this website or reliance on its content.</p>'
            . '<h3>Changes</h3>'
            . '<p>We may update these Terms of Use from time to time. Continued use of the site after changes constitutes acceptance of the updated terms.</p>'
            . '<h3>Contact</h3>'
            . '<p>For questions about these Terms of Use, please contact us using the details on our website.</p>';

        DB::table('cms_pages')->insert([
            'slug' => 'terms-of-use',
            'name' => 'Terms Of Use',
            'heading' => 'Terms Of Use',
            'content' => $content,
            'meta_title' => 'Terms Of Use | ' . config('app.name'),
            'meta_description' => 'Terms of Use for our website. Please read the conditions that apply when you use our property listings and services.',
            'meta_keywords' => 'terms of use, terms and conditions, website terms',
            'canonical_url' => null,
            'banner_image' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('cms_pages')->where('slug', 'terms-of-use')->delete();
    }
};
