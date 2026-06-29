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
        Schema::create('certificate_layouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('season_id')->unique()->constrained()->onDelete('cascade');
            $table->string('template_path')->nullable();
            $table->string('font_path')->nullable();
            $table->integer('font_size')->default(48);
            $table->string('font_color')->default('#ffc107');
            $table->double('pos_x')->default(50.0);
            $table->double('pos_y')->default(50.0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_layouts');
    }
};
