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
        Schema::create('bracket_snapshots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('season_id');
            $table->integer('step_number')->default(1);
            $table->boolean('is_current')->default(false);
            $table->string('action_name')->default('Update Bagan');
            $table->longText('bracket_data');
            $table->longText('team_data');
            $table->text('season_meta')->nullable();
            $table->timestamps();

            $table->index('season_id');
            $table->index(['season_id', 'step_number']);
            $table->index(['season_id', 'is_current']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bracket_snapshots');
    }
};
