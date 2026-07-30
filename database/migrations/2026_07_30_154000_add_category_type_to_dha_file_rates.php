<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        migration_add_column_if_missing('dha_file_rates', 'category', function (Blueprint $table) {
            $table->string('category', 40)->nullable()->after('plot_size');
        });

        migration_add_column_if_missing('dha_file_rates', 'file_type', function (Blueprint $table) {
            $table->string('file_type', 120)->nullable()->after('category');
        });

        migration_add_column_if_missing('dha_file_rates', 'price_digits', function (Blueprint $table) {
            $table->string('price_digits', 60)->nullable()->after('price');
        });

        if (Schema::hasTable('dha_file_rates') && Schema::hasColumn('dha_file_rates', 'category')) {
            $hasAny = DB::table('dha_file_rates')->whereNotNull('category')->exists();
            if (! $hasAny) {
                DB::table('dha_file_rates')->update([
                    'category' => 'residential',
                    'file_type' => 'Allocation',
                ]);
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('dha_file_rates')) {
            return;
        }

        Schema::table('dha_file_rates', function (Blueprint $table) {
            if (Schema::hasColumn('dha_file_rates', 'price_digits')) {
                $table->dropColumn('price_digits');
            }
            if (Schema::hasColumn('dha_file_rates', 'file_type')) {
                $table->dropColumn('file_type');
            }
            if (Schema::hasColumn('dha_file_rates', 'category')) {
                $table->dropColumn('category');
            }
        });
    }
};
