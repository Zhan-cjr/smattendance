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
        Schema::create('kontrak', function (Blueprint $table) {
            $table->id();
            $table->string('no_kontrak', 100)->unique();
            $table->string('nik', 50)->index();
            $table->date('tanggal')->nullable();
            $table->date('dari')->nullable();
            $table->date('sampai')->nullable();
            $table->string('kode_jabatan', 50)->nullable()->index();
            $table->string('kode_cabang', 50)->nullable()->index();
            $table->string('kode_dept', 50)->nullable()->index();
            $table->string('status_kontrak', 20)->nullable()->index();
            $table->string('kode_gaji', 50)->nullable()->index();
            $table->string('kode_tunjangan', 50)->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kontrak');
    }
};
