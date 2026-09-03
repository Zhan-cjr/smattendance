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
        Schema::create('pinjaman', function (Blueprint $table) {
            $table->id();
            $table->char('nik', 10);
            $table->string('no_pinjaman', 20)->unique();
            $table->date('tanggal_pinjaman');
            $table->decimal('jumlah_pinjaman', 15, 2);
            $table->smallInteger('jumlah_cicilan');
            $table->decimal('jumlah_per_cicilan', 15, 2);
            $table->decimal('total_dibayar', 15, 2)->default(0);
            $table->decimal('sisa_pinjaman', 15, 2);
            $table->text('keterangan')->nullable();
            $table->char('status', 1)->default('A'); // A=Aktif, L=Lunas, B=Batal
            $table->smallInteger('bulan_mulai_cicilan');
            $table->char('tahun_mulai_cicilan', 4);
            $table->timestamps();

            $table->foreign('nik')->references('nik')->on('karyawan')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pinjaman');
    }
};
