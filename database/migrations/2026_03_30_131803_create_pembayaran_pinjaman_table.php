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
        Schema::create('pembayaran_pinjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pinjaman_id')->constrained('pinjaman')->onDelete('cascade');
            $table->foreignId('rencana_cicilan_id')->nullable()->constrained('rencana_cicilan')->onDelete('set null');
            $table->date('tanggal_bayar');
            $table->smallInteger('bulan_gaji');
            $table->char('tahun_gaji', 4);
            $table->decimal('jumlah_bayar', 15, 2);
            $table->char('jenis_pembayaran', 1); // C=Cicilan, P=Pelunasan, M=Manual
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_pinjaman');
    }
};
