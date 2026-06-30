<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('homepage_choice_slides')) {
            return;
        }

        if (! Schema::hasColumn('homepage_choice_slides', 'card_image')) {
            Schema::table('homepage_choice_slides', function (Blueprint $table) {
                $table->string('card_image')->nullable()->after('description');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('homepage_choice_slides') && Schema::hasColumn('homepage_choice_slides', 'card_image')) {
            Schema::table('homepage_choice_slides', function (Blueprint $table) {
                $table->dropColumn('card_image');
            });
        }
    }
};
