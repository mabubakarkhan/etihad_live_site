<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('label')->nullable();
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('label')->nullable();
            $table->timestamps();
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['role_id', 'user_id']);
        });

        Schema::create('permission_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['permission_id', 'role_id']);
        });

        // Seed a default "admin" role and attach it to the existing admin user if present
        $adminRoleId = DB::table('roles')->insertGetId([
            'name' => 'admin',
            'label' => 'Administrator',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $defaultPermissions = [
            'manage_users' => 'Manage users, roles, and permissions',
            'view_activity_logs' => 'View system activity logs',
        ];

        foreach ($defaultPermissions as $name => $label) {
            $permissionId = DB::table('permissions')->insertGetId([
                'name' => $name,
                'label' => $label,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('permission_role')->insert([
                'permission_id' => $permissionId,
                'role_id' => $adminRoleId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $adminUser = DB::table('users')->where('username', 'admin')->first();

        if ($adminUser) {
            DB::table('role_user')->insert([
                'role_id' => $adminRoleId,
                'user_id' => $adminUser->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};

