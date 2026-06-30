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
        Schema::table('match_reports', function (Blueprint $table) {
            $table->string('ai_status')->default('PENDING')->after('status');
            $table->text('ai_notes')->nullable()->after('ai_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('match_reports', function (Blueprint $table) {
            $table->dropColumn(['ai_status', 'ai_notes']);
        });
    }
};
