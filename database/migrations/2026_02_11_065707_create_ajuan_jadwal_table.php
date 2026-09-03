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
        Schema::create('ajuan_jadwal', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 20);
            $table->date('tanggal');
            $table->string('kode_jam_kerja_awal', 10)->nullable();
            $table->string('kode_jam_kerja_tujuan', 10);
            $table->text('keterangan')->nullable();
            $table->char('status', 1)->default('p')->comment('p=pending, a=approved, r=rejected');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ajuan_jadwal');
    }
};
