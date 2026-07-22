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
        Schema::table('seasons', function (Blueprint $table) {
            $table->string('manual_juara1')->nullable()->after('is_bracket_visible');
            $table->string('manual_juara2')->nullable()->after('manual_juara1');
            $table->string('manual_juara3')->nullable()->after('manual_juara2');
            $table->string('manual_juara4')->nullable()->after('manual_juara3');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seasons', function (Blueprint $table) {
            $table->dropColumn(['manual_juara1', 'manual_juara2', 'manual_juara3', 'manual_juara4']);
        });
    }
};
