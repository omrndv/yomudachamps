<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Define all 12 available permissions
        $allPermissions = [
            "dashboard",
            "seasons",
            "teams",
            "payments",
            "notes",
            "settings",
            "faqs",
            "activity_log",
            "manage",
            "backup",
            "finance",
            "solo_matchmaker"
        ];

        // Update all existing admin users to have all permissions active
        DB::table('users')->where('role', 'admin')->update([
            'permissions' => json_encode($allPermissions)
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original default permissions if needed
        $defaultPermissions = ["dashboard", "seasons", "notes", "faqs", "activity_log"];
        DB::table('users')->where('role', 'admin')->update([
            'permissions' => json_encode($defaultPermissions)
        ]);
    }
};
