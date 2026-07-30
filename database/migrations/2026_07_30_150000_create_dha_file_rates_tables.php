<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        migration_create_table('dha_file_rate_settings', function (Blueprint $table) {
            $table->id();
            $table->string('heading')->nullable();
            $table->string('subheading')->nullable();
            $table->text('details')->nullable();
            $table->string('default_cta_label')->nullable();
            $table->string('default_cta_url')->nullable();
            $table->boolean('is_published')->default(true);
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('meta_robots')->nullable();
            $table->string('og_title')->nullable();
            $table->string('og_description', 500)->nullable();
            $table->string('twitter_card')->nullable();
            $table->timestamps();
        });

        migration_create_table('dha_file_rates', function (Blueprint $table) {
            $table->id();
            $table->string('plot_size');
            $table->foreignId('dha_phase_id')->nullable()->constrained('dha_phases')->nullOnDelete();
            $table->string('price')->nullable();
            $table->string('cta_label')->nullable();
            $table->string('cta_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        migration_seed_if_empty('dha_file_rate_settings', function () {
            DB::table('dha_file_rate_settings')->insert([
                'heading' => 'DHA File Rates',
                'subheading' => 'Current DHA Lahore file rates by phase & plot size',
                'details' => 'Browse the latest DHA file rates across phases. Prices are indicative and may change — contact our advisors for the latest availability and deals.',
                'default_cta_label' => 'Enquire Now',
                'default_cta_url' => null,
                'is_published' => true,
                'meta_title' => 'DHA File Rates Lahore | Latest Phase Prices | Etihad Marketing',
                'meta_description' => 'Check current DHA Lahore file rates by phase and plot size. Compare prices and enquire with Etihad Marketing advisors for the latest deals.',
                'meta_keywords' => 'DHA file rates, DHA Lahore rates, DHA phase prices, DHA plot rates, Etihad Marketing',
                'canonical_url' => null,
                'meta_robots' => 'index, follow',
                'og_title' => 'DHA File Rates Lahore | Etihad Marketing',
                'og_description' => 'Latest DHA Lahore file rates by phase and plot size. Enquire with Etihad Marketing.',
                'twitter_card' => 'summary_large_image',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        migration_seed_if_empty('dha_file_rates', function () {
            $phaseIds = DB::table('dha_phases')->orderBy('sort_order')->orderBy('id')->limit(3)->pluck('id')->all();
            $phase1 = $phaseIds[0] ?? null;
            $phase2 = $phaseIds[1] ?? $phase1;
            $phase3 = $phaseIds[2] ?? $phase2;

            $rows = [
                ['plot_size' => '5 Marla', 'dha_phase_id' => $phase1, 'price' => 'Rs. 1.85 Crore', 'cta_label' => 'Enquire Now', 'sort_order' => 1],
                ['plot_size' => '10 Marla', 'dha_phase_id' => $phase1, 'price' => 'Rs. 3.40 Crore', 'cta_label' => 'Enquire Now', 'sort_order' => 2],
                ['plot_size' => '1 Kanal', 'dha_phase_id' => $phase2, 'price' => 'Rs. 6.25 Crore', 'cta_label' => 'Enquire Now', 'sort_order' => 3],
                ['plot_size' => '5 Marla', 'dha_phase_id' => $phase2, 'price' => 'Rs. 1.55 Crore', 'cta_label' => 'Enquire Now', 'sort_order' => 4],
                ['plot_size' => '10 Marla', 'dha_phase_id' => $phase3, 'price' => 'Rs. 2.90 Crore', 'cta_label' => 'Enquire Now', 'sort_order' => 5],
            ];

            foreach ($rows as $row) {
                DB::table('dha_file_rates')->insert(array_merge($row, [
                    'cta_url' => null,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dha_file_rates');
        Schema::dropIfExists('dha_file_rate_settings');
    }
};
