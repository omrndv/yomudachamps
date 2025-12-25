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
            // Kita tambahin 3 kolom penting buat Tripay
            $table->string('tripay_reference')->nullable()->after('trx_id');
            $table->string('payment_method')->nullable()->after('tripay_reference');
            $table->string('status_tripay')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn(['tripay_reference', 'payment_method', 'status_tripay']);
        });
    }
};
