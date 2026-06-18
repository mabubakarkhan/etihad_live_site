<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('contact_settings', 'twitter')) {
                $table->string('twitter')->nullable()->after('youtube');
            }
            if (!Schema::hasColumn('contact_settings', 'tiktok')) {
                $table->string('tiktok')->nullable()->after('twitter');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contact_settings', function (Blueprint $table) {
            $dropColumns = [];
            if (Schema::hasColumn('contact_settings', 'twitter')) {
                $dropColumns[] = 'twitter';
            }
            if (Schema::hasColumn('contact_settings', 'tiktok')) {
                $dropColumns[] = 'tiktok';
            }
            if (!empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
