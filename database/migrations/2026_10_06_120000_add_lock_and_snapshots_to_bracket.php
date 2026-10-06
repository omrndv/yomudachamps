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
        if (Schema::hasTable('seasons') && !Schema::hasColumn('seasons', 'is_bracket_locked')) {
            Schema::table('seasons', function (Blueprint $table) {
                $table->boolean('is_bracket_locked')->default(false)->after('is_bracket_visible');
            });
        }

        if (Schema::hasTable('brackets')) {
            Schema::table('brackets', function (Blueprint $table) {
                if (!Schema::hasColumn('brackets', 'team1_name_snapshot')) {
                    $table->string('team1_name_snapshot')->nullable()->after('team1_id');
                }
                if (!Schema::hasColumn('brackets', 'team2_name_snapshot')) {
                    $table->string('team2_name_snapshot')->nullable()->after('team2_id');
                }
                if (!Schema::hasColumn('brackets', 'winner_name_snapshot')) {
                    $table->string('winner_name_snapshot')->nullable()->after('winner_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('seasons') && Schema::hasColumn('seasons', 'is_bracket_locked')) {
            Schema::table('seasons', function (Blueprint $table) {
                $table->dropColumn('is_bracket_locked');
            });
        }

        if (Schema::hasTable('brackets')) {
            Schema::table('brackets', function (Blueprint $table) {
                $columns = [];
                if (Schema::hasColumn('brackets', 'team1_name_snapshot')) {
                    $columns[] = 'team1_name_snapshot';
                }
                if (Schema::hasColumn('brackets', 'team2_name_snapshot')) {
                    $columns[] = 'team2_name_snapshot';
                }
                if (Schema::hasColumn('brackets', 'winner_name_snapshot')) {
                    $columns[] = 'winner_name_snapshot';
                }
                if (!empty($columns)) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
};
