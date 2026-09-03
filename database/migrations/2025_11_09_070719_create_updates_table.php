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
        Schema::create('updates', function (Blueprint $table) {
            $table->id();
            $table->string('version')->unique(); // Versi update (contoh: 1.0.0, 1.0.1)
            $table->string('title')->nullable(); // Judul update
            $table->text('description')->nullable(); // Deskripsi update
            $table->text('changelog')->nullable(); // Changelog detail
            $table->string('file_url')->nullable(); // URL file update (ZIP)
            $table->string('file_size')->nullable(); // Ukuran file dalam bytes
            $table->string('checksum')->nullable(); // MD5 atau SHA256 checksum untuk validasi
            $table->boolean('is_major')->default(false); // Apakah update major (wajib update)
            $table->boolean('is_active')->default(true); // Apakah update aktif
            $table->json('migrations')->nullable(); // Daftar migration yang perlu dijalankan
            $table->json('seeders')->nullable(); // Daftar seeder yang perlu dijalankan
            $table->timestamp('released_at')->nullable(); // Tanggal rilis update
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('updates');
    }
};
