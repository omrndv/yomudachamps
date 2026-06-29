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
        Schema::create('season_chats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('season_id');
            $table->string('sender_session_token');
            $table->string('sender_name');
            $table->text('message');
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            // Indexes for speedy querying
            $table->index('season_id');
            $table->index('sender_session_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('season_chats');
    }
};
