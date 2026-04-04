<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('careers', function (Blueprint $table) {
            $table->string('education', 255)->nullable()->after('department');
            $table->string('experience', 255)->nullable()->after('education');
            $table->string('timings', 255)->nullable()->after('experience');
            $table->string('joining_month', 60)->nullable()->after('timings');

            $table->string('employment_type', 80)->nullable()->after('joining_month');
            $table->string('salary_range', 120)->nullable()->after('employment_type');
            $table->unsignedInteger('vacancies')->nullable()->after('salary_range');
            $table->date('apply_before')->nullable()->after('vacancies');

            $table->string('apply_email', 255)->nullable()->after('apply_before');
            $table->string('apply_url', 500)->nullable()->after('apply_email');

            $table->string('meta_title')->nullable()->after('apply_url');
            $table->string('meta_description', 500)->nullable()->after('meta_title');
            $table->string('meta_keywords', 500)->nullable()->after('meta_description');
            $table->string('canonical_url', 500)->nullable()->after('meta_keywords');
        });
    }

    public function down(): void
    {
        Schema::table('careers', function (Blueprint $table) {
            $table->dropColumn([
                'education',
                'experience',
                'timings',
                'joining_month',
                'employment_type',
                'salary_range',
                'vacancies',
                'apply_before',
                'apply_email',
                'apply_url',
                'meta_title',
                'meta_description',
                'meta_keywords',
                'canonical_url',
            ]);
        });
    }
};

