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
        Schema::create('pinjaman_generate_history', function (Blueprint $table) {
            $table->id();
            $table->string('bulan', 2);
            $table->string('tahun', 4);
            $table->date('tanggal_generate');
            $table->integer('user_id');
            $table->timestamps();
        });

        Schema::table('pembayaran_pinjaman', function (Blueprint $table) {
            $table->unsignedBigInteger('history_generate_id')->nullable()->after('rencana_cicilan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_pinjaman', function (Blueprint $table) {
            $table->dropColumn('history_generate_id');
        });
        Schema::dropIfExists('pinjaman_generate_history');
    }
};
