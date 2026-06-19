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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable()->after('name');
            $table->string('role')->default('admin')->after('password');
        });

        // Seed data
        $users = [
            [
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'email' => 'superadmin@yomuda.com',
                'password' => Hash::make('bosadmin'),
                'role' => 'superadmin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Nanda',
                'username' => 'nanda',
                'email' => 'nanda@yomuda.com',
                'password' => Hash::make('yomuda123'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Amar',
                'username' => 'amar',
                'email' => 'amar@yomuda.com',
                'password' => Hash::make('yomuda123'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Nadiv',
                'username' => 'nadiv',
                'email' => 'nadiv@yomuda.com',
                'password' => Hash::make('yomuda123'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Yunan',
                'username' => 'yunan',
                'email' => 'yunan@yomuda.com',
                'password' => Hash::make('yomuda123'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Abyan',
                'username' => 'abyan',
                'email' => 'abyan@yomuda.com',
                'password' => Hash::make('yomuda123'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Farel',
                'username' => 'farel',
                'email' => 'farel@yomuda.com',
                'password' => Hash::make('yomuda123'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Faiz',
                'username' => 'faiz',
                'email' => 'faiz@yomuda.com',
                'password' => Hash::make('yomuda123'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Candra',
                'username' => 'candra',
                'email' => 'candra@yomuda.com',
                'password' => Hash::make('yomuda123'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Elgi',
                'username' => 'elgi',
                'email' => 'elgi@yomuda.com',
                'password' => Hash::make('yomuda123'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('users')->insert($users);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'role']);
        });
    }
};
