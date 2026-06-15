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
            if (!Schema::hasColumn('seasons', 'poster')) {
                $table->string('poster')->nullable(); // Untuk simpan nama file gambar
            }
            if (!Schema::hasColumn('seasons', 'prize_pool')) {
                $table->string('prize_pool')->default('Rp 400.000'); // Untuk teks prize pool
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seasons', function (Blueprint $table) {
            if (Schema::hasColumn('seasons', 'poster')) {
                $table->dropColumn('poster');
            }
            if (Schema::hasColumn('seasons', 'prize_pool')) {
                $table->dropColumn('prize_pool');
            }
        });
    }
};
