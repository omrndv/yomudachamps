<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('permissions')->nullable()->after('role');
        });

        // Set default permissions for existing admin users
        $defaultPermissions = json_encode(["seasons", "finance", "solo_matchmaker", "notes", "faqs", "activity_log"]);
        DB::table('users')->where('role', 'admin')->update([
            'permissions' => $defaultPermissions
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('permissions');
        });
    }
};
