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
        Schema::create('lembur_aturan', function (Blueprint $table) {
            $table->id();
            $table->char('tipe_hari', 1); // 1: Hari Kerja, 2: Hari Libur
            $table->decimal('jam_dari', 5, 2);
            $table->decimal('jam_sampai', 5, 2)->nullable();
            $table->decimal('faktor', 5, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lembur_aturan');
    }
};
