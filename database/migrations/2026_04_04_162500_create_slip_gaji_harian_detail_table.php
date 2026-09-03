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
        Schema::create('slip_gaji_harian_detail', function (Blueprint $table) {
            $table->id();
            $table->string('kode_slip_gaji_harian', 20);
            $table->string('nik', 20);
            $table->foreign('kode_slip_gaji_harian')->references('kode_slip_gaji_harian')->on('slip_gaji_harian')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slip_gaji_harian_detail');
    }
};
