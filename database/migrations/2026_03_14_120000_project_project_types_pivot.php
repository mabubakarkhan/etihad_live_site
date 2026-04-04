<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_project_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('project_type_id')->constrained('project_types')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['project_id', 'project_type_id']);
        });

        // Migrate existing single type to pivot
        DB::table('projects')->whereNotNull('project_type_id')->orderBy('id')->chunk(100, function ($projects) {
            foreach ($projects as $p) {
                DB::table('project_project_type')->insert([
                    'project_id' => $p->id,
                    'project_type_id' => $p->project_type_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['project_type_id']);
            $table->dropColumn('project_type_id');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('project_type_id')->nullable()->after('id')->constrained('project_types')->nullOnDelete();
        });

        $firstType = DB::table('project_project_type')->select('project_id', 'project_type_id')->get()->groupBy('project_id');
        foreach ($firstType as $projectId => $rows) {
            $typeId = $rows->first()->project_type_id;
            DB::table('projects')->where('id', $projectId)->update(['project_type_id' => $typeId]);
        }

        Schema::dropIfExists('project_project_type');

        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['project_type_id']);
        });
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('project_type_id')->nullable()->constrained('project_types')->nullOnDelete();
        });
    }
};
