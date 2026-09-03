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
        Schema::create('rencana_cicilan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pinjaman_id')->constrained('pinjaman')->onDelete('cascade');
            $table->smallInteger('cicilan_ke');
            $table->smallInteger('bulan');
            $table->char('tahun', 4);
            $table->decimal('jumlah_cicilan', 15, 2);
            $table->char('status', 1)->default('B'); // B=Belum, S=Sudah, L=Lewat
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rencana_cicilan');
    }
};
