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
        Schema::create('mesin_fingerprints', function (Blueprint $table) {
            $table->id();
            $table->string('sn')->unique()->comment('Serial Number / Dev-ID Mesin');
            $table->string('nama_mesin');
            $table->string('merk')->nullable();
            $table->string('lokasi')->nullable();
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mesin_fingerprints');
    }
};
