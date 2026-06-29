<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('match_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bracket_id')->constrained('brackets')->onDelete('cascade');
            $table->foreignId('season_id')->constrained('seasons')->onDelete('cascade');
            $table->foreignId('reporter_team_id')->constrained('teams')->onDelete('cascade');
            $table->integer('score_team1');
            $table->integer('score_team2');
            $table->string('image_proof');
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED'])->default('PENDING');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_reports');
    }
};
