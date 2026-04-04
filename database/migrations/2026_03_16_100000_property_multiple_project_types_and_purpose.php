<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_project_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->foreignId('project_type_id')->constrained('project_types')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['property_id', 'project_type_id']);
        });

        $properties = DB::table('properties')->whereNotNull('project_type_id')->get();
        foreach ($properties as $p) {
            DB::table('property_project_type')->insert([
                'property_id' => $p->id,
                'project_type_id' => $p->project_type_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('properties', function (Blueprint $table) {
            $table->string('purpose', 20)->default('sale')->after('dealer_id');
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->dropForeign(['project_type_id']);
            $table->dropColumn('project_type_id');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->foreignId('project_type_id')->nullable()->after('status')->constrained('project_types')->nullOnDelete();
        });

        $pivot = DB::table('property_project_type')->select('property_id', 'project_type_id')->get()->groupBy('property_id');
        foreach ($pivot as $propertyId => $rows) {
            $first = $rows->first();
            DB::table('properties')->where('id', $propertyId)->update(['project_type_id' => $first->project_type_id]);
        }

        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('purpose');
        });

        Schema::dropIfExists('property_project_type');
    }
};
