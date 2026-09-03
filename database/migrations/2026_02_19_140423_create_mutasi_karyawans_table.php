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
        Schema::create('mutasi_karyawan', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 10)->index();
            $table->date('tanggal_mutasi');
            $table->enum('jenis_mutasi', ['MUTASI', 'PROMOSI', 'DEMOSI']);
            
            $table->string('kode_cabang_lama', 10)->nullable();
            $table->string('kode_cabang_baru', 10)->nullable();
            
            $table->string('kode_dept_lama', 10)->nullable();
            $table->string('kode_dept_baru', 10)->nullable();
            
            $table->string('kode_jabatan_lama', 10)->nullable();
            $table->string('kode_jabatan_baru', 10)->nullable();

            $table->string('status_karyawan_lama', 10)->nullable();
            $table->string('status_karyawan_baru', 10)->nullable();
            
            $table->text('keterangan')->nullable();
            $table->string('doc_sk')->nullable();
            $table->unsignedBigInteger('user_id')->nullable(); // Created by which user
            $table->timestamps();

            $table->foreign('nik')->references('nik')->on('karyawan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutasi_karyawans');
    }
};
