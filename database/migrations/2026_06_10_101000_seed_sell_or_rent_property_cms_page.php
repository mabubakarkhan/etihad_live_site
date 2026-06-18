<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $app = config('app.name', 'Etihad Marketing');
        $now = now();

        $payload = [
            'name' => 'Sell or Rent Property',
            'heading' => 'Sell or Rent Your Property with Confidence',
            'content' => '<p>Expert help, zero hassle. List your property in DHA Lahore with Etihad Marketing and connect with trusted agents from start to finish.</p>',
            'meta_title' => 'Sell or Rent Your Property in DHA Lahore | ' . $app,
            'meta_description' => 'Sell or rent your property in DHA Lahore with confidence. Get expert guidance, market insights, and verified agent support from Etihad Marketing.',
            'meta_keywords' => 'sell property DHA Lahore, rent property DHA, list property Lahore, Etihad Marketing, DHA real estate',
            'canonical_url' => null,
            'banner_image' => null,
            'updated_at' => $now,
        ];

        if (DB::table('cms_pages')->where('slug', 'sell-or-rent-property')->exists()) {
            DB::table('cms_pages')->where('slug', 'sell-or-rent-property')->update($payload);
        } else {
            DB::table('cms_pages')->insert(array_merge(['slug' => 'sell-or-rent-property'], $payload, ['created_at' => $now]));
        }
    }

    public function down(): void
    {
        DB::table('cms_pages')->where('slug', 'sell-or-rent-property')->delete();
    }
};
