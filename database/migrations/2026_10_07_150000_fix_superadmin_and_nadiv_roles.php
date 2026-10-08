<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Pastikan superadmin memakai email dummy super@gmail.com dan role superadmin, tanpa google_id
        DB::table('users')->where('username', 'superadmin')->update([
            'email' => 'super@gmail.com',
            'role' => 'superadmin',
            'google_id' => null,
            'updated_at' => now(),
        ]);

        // 2. Jika ada akun lain selain nadiv yang masih memegang umarnadiv@gmail.com, ubah emailnya
        $existingNadiv = DB::table('users')->where('username', 'nadiv')->first();
        if ($existingNadiv) {
            $duplicateUsers = DB::table('users')->where('email', 'umarnadiv@gmail.com')
                ->where('id', '!=', $existingNadiv->id)
                ->get();
            foreach ($duplicateUsers as $dup) {
                DB::table('users')->where('id', $dup->id)->update([
                    'email' => 'archived_' . $dup->id . '_' . time() . '@yomuda.local',
                    'updated_at' => now(),
                ]);
            }
        }

        // 3. Pastikan akun nadiv memiliki role 'admin' (bukan superadmin), email umarnadiv@gmail.com, is_active true
        $allPermissions = json_encode([
            "dashboard", "seasons", "teams", "payments", "notes",
            "settings", "gateway_notifications", "faqs", "activity_log",
            "manage", "laravel_logs", "storage", "backup", "finance", "solo_matchmaker"
        ]);

        $targetId = null;
        if ($existingNadiv) {
            $targetId = $existingNadiv->id;
        } else {
            $user4 = DB::table('users')->where('id', 4)->first();
            if ($user4 && $user4->username !== 'superadmin') {
                $targetId = 4;
            } else {
                $userByEmail = DB::table('users')->where('email', 'umarnadiv@gmail.com')->where('username', '!=', 'superadmin')->first();
                if ($userByEmail) {
                    $targetId = $userByEmail->id;
                }
            }
        }

        if ($targetId) {
            DB::table('users')->where('id', $targetId)->update([
                'username' => 'nadiv',
                'email' => 'umarnadiv@gmail.com',
                'role' => 'admin',
                'is_active' => 1,
                'permissions' => $allPermissions,
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
