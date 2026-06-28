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
        // Define default permissions (7 pages)
        $defaultPermissions = [
            "dashboard",
            "seasons",
            "notes",
            "activity_log",
            "faqs",
            "finance",
            "solo_matchmaker"
        ];

        // Update all existing admin users to have default permissions active
        DB::table('users')->where('role', 'admin')->update([
            'permissions' => json_encode($defaultPermissions)
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
