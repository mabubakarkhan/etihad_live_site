<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dha_phases', function (Blueprint $table) {
            $table->json('value_propositions')->nullable()->after('contact_intro');
            $table->string('attractions_heading')->nullable()->after('value_propositions');
            $table->json('attractions')->nullable()->after('attractions_heading');
            $table->json('investment_reasons')->nullable()->after('attractions');
            $table->json('project_highlights')->nullable()->after('investment_reasons');
            $table->string('help_bar_eyebrow')->nullable()->after('project_highlights');
            $table->string('help_bar_title')->nullable()->after('help_bar_eyebrow');
            $table->text('help_bar_text')->nullable()->after('help_bar_title');
        });
    }

    public function down(): void
    {
        Schema::table('dha_phases', function (Blueprint $table) {
            $table->dropColumn([
                'value_propositions',
                'attractions_heading',
                'attractions',
                'investment_reasons',
                'project_highlights',
                'help_bar_eyebrow',
                'help_bar_title',
                'help_bar_text',
            ]);
        });
    }
};
