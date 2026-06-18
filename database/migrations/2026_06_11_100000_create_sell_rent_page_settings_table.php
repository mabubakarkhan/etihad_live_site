<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sell_rent_page_settings', function (Blueprint $table) {
            $table->id();
            $table->string('hero_image')->nullable();
            $table->json('hero_bubbles')->nullable();
            $table->string('valuation_heading')->nullable();
            $table->string('valuation_price')->nullable();
            $table->string('valuation_badge')->nullable();
            $table->json('valuation_meta')->nullable();
            $table->string('valuation_chart_image')->nullable();
            $table->text('valuation_copy')->nullable();
            $table->string('transactions_heading')->nullable();
            $table->json('transaction_stats')->nullable();
            $table->json('transactions')->nullable();
            $table->text('transactions_copy')->nullable();
            $table->string('faqs_heading')->nullable();
            $table->json('faqs')->nullable();
            $table->string('form_submit_label')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        $seed = require database_path('data/sell_rent_page_seed.php');
        $now = now();

        DB::table('sell_rent_page_settings')->insert([
            'hero_image' => $seed['hero_image'],
            'hero_bubbles' => json_encode($seed['hero_bubbles']),
            'valuation_heading' => $seed['valuation_heading'],
            'valuation_price' => $seed['valuation_price'],
            'valuation_badge' => $seed['valuation_badge'],
            'valuation_meta' => json_encode($seed['valuation_meta']),
            'valuation_chart_image' => $seed['valuation_chart_image'],
            'valuation_copy' => $seed['valuation_copy'],
            'transactions_heading' => $seed['transactions_heading'],
            'transaction_stats' => json_encode($seed['transaction_stats']),
            'transactions' => json_encode($seed['transactions']),
            'transactions_copy' => $seed['transactions_copy'],
            'faqs_heading' => $seed['faqs_heading'],
            'faqs' => json_encode($seed['faqs']),
            'form_submit_label' => $seed['form_submit_label'],
            'status' => $seed['status'],
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('sell_rent_page_settings');
    }
};
