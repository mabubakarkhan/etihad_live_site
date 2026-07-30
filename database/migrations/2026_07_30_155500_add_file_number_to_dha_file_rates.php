<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        migration_add_column_if_missing('dha_file_rates', 'file_number', function (Blueprint $table) {
            $table->string('file_number', 120)->nullable()->after('id');
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('dha_file_rates') && Schema::hasColumn('dha_file_rates', 'file_number')) {
            Schema::table('dha_file_rates', function (Blueprint $table) {
                $table->dropColumn('file_number');
            });
        }
    }
};
