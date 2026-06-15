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
        Schema::table('teams', function (Blueprint $table) {
            if (!Schema::hasColumn('teams', 'amount')) {
                $table->integer('amount')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('teams', 'fee')) {
                $table->integer('fee')->nullable()->after('amount');
            }
            if (!Schema::hasColumn('teams', 'net_amount')) {
                $table->integer('net_amount')->nullable()->after('fee');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            if (Schema::hasColumn('teams', 'amount')) {
                $table->dropColumn('amount');
            }
            if (Schema::hasColumn('teams', 'fee')) {
                $table->dropColumn('fee');
            }
            if (Schema::hasColumn('teams', 'net_amount')) {
                $table->dropColumn('net_amount');
            }
        });
    }
};
