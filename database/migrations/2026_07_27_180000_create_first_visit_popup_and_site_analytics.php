<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('first_visit_popup_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_enabled')->default(true);
            $table->string('eyebrow', 120)->nullable();
            $table->string('heading')->nullable();
            $table->string('subheading', 500)->nullable();
            $table->text('body_text')->nullable();
            $table->string('cta_label', 120)->nullable();
            $table->string('form_heading', 255)->nullable();
            $table->string('form_submit_label', 120)->nullable();
            $table->string('background_image')->nullable();
            $table->boolean('show_logo')->default(true);
            $table->unsignedSmallInteger('delay_ms')->default(0);
            $table->timestamps();
        });

        DB::table('first_visit_popup_settings')->insert([
            'is_enabled' => true,
            'eyebrow' => 'DHA PHASE X — THE FUTURE OF LAHORE',
            'heading' => 'We proudly unveil Phase-X – “The Future of Lahore”',
            'subheading' => 'The journey of long-term vision of DHA Lahore begins in August 2026.',
            'body_text' => 'Designed around integrated living, business, mobility, green infrastructure & long-term service standards.',
            'cta_label' => 'Contact Us',
            'form_heading' => 'Get in touch with Etihad',
            'form_submit_label' => 'Submit',
            'background_image' => null,
            'show_logo' => true,
            'delay_ms' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (! Schema::hasColumn('contact_messages', 'source')) {
            Schema::table('contact_messages', function (Blueprint $table) {
                $table->string('source', 40)->default('contact_form')->after('status')->index();
                $table->string('city', 120)->nullable()->after('phone');
            });
        }

        if (! Schema::hasColumn('visitor_daily_counts', 'page_views')) {
            Schema::table('visitor_daily_counts', function (Blueprint $table) {
                $table->unsignedBigInteger('page_views')->default(0)->after('count_projects');
                $table->unsignedBigInteger('first_visitors')->default(0)->after('page_views');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('visitor_daily_counts', 'page_views')) {
            Schema::table('visitor_daily_counts', function (Blueprint $table) {
                $table->dropColumn(['page_views', 'first_visitors']);
            });
        }

        if (Schema::hasColumn('contact_messages', 'source')) {
            Schema::table('contact_messages', function (Blueprint $table) {
                $table->dropColumn(['source', 'city']);
            });
        }

        Schema::dropIfExists('first_visit_popup_settings');
    }
};
