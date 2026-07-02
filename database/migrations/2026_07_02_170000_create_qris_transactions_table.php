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
        Schema::create('qris_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('trx_id')->index(); // Menghubungkan ke teams.trx_id
            $table->bigInteger('base_amount');
            $table->integer('unique_code');
            $table->bigInteger('amount'); // base_amount + unique_code
            $table->text('qris_string');
            $table->string('status')->default('PENDING'); // PENDING, PAID, EXPIRED
            $table->dateTime('expires_at');
            $table->dateTime('paid_at')->nullable();
            $table->string('gopay_reference')->nullable(); // ID Transaksi GoPay
            $table->timestamps();

            // Mencegah double pending dengan nominal unik yang sama
            $table->unique(['amount', 'status'], 'unique_pending_amount_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qris_transactions');
    }
};
