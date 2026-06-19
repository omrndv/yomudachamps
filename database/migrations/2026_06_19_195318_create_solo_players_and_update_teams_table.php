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
        Schema::create('solo_players', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('season_id');
            $table->unsignedBigInteger('team_id')->nullable();
            $table->string('name');
            $table->string('wa_number');
            $table->string('role');
            $table->string('rank');
            $table->string('status')->default('PENDING'); // PENDING, PAID
            $table->integer('amount_paid')->default(0);
            $table->timestamps();

            $table->foreign('season_id')->references('id')->on('seasons')->onDelete('cascade');
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->boolean('is_solo_team')->default(false)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solo_players');

        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('is_solo_team');
        });
    }
};
