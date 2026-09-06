<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('role');
            }
            if (!Schema::hasColumn('users', 'last_seen_at')) {
                $table->timestamp('last_seen_at')->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('users', 'force_logout_at')) {
                $table->timestamp('force_logout_at')->nullable()->after('last_seen_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('users', 'is_active')) $columns[] = 'is_active';
            if (Schema::hasColumn('users', 'last_seen_at')) $columns[] = 'last_seen_at';
            if (Schema::hasColumn('users', 'force_logout_at')) $columns[] = 'force_logout_at';
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
