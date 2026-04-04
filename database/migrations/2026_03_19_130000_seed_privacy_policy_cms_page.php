<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('cms_pages')->where('slug', 'privacy-policy')->exists()) {
            return;
        }

        $content = '<p>This Privacy Policy describes how we collect, use, and protect your personal information when you use our website and services.</p>'
            . '<h3>Information We Collect</h3>'
            . '<p>We may collect information you provide when you enquire about properties or projects, request a showing, or contact us. This may include your name, phone number, email address, and any message you send.</p>'
            . '<h3>How We Use Your Information</h3>'
            . '<p>We use your information to respond to your enquiries, process showing requests, and improve our services. We do not sell your personal data to third parties.</p>'
            . '<h3>Data Security</h3>'
            . '<p>We take reasonable steps to protect your personal information from unauthorised access, loss, or misuse.</p>'
            . '<h3>Cookies</h3>'
            . '<p>Our website may use cookies to improve your experience. You can adjust your browser settings to manage or disable cookies.</p>'
            . '<h3>Contact</h3>'
            . '<p>If you have questions about this Privacy Policy or your data, please contact us using the details provided on our website.</p>';

        DB::table('cms_pages')->insert([
            'slug' => 'privacy-policy',
            'name' => 'Privacy Policy',
            'heading' => 'Privacy Policy',
            'content' => $content,
            'meta_title' => 'Privacy Policy | ' . config('app.name'),
            'meta_description' => 'Read our Privacy Policy. We explain how we collect, use, and protect your information when you use our website and property services.',
            'meta_keywords' => 'privacy policy, data protection, personal information, cookies',
            'canonical_url' => null,
            'banner_image' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('cms_pages')->where('slug', 'privacy-policy')->delete();
    }
};
