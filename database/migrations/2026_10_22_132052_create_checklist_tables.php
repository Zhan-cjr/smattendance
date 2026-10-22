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
        // 1. Master Checkpoints
        Schema::create('checklist_checkpoints', function (Blueprint $table) {
            $table->id();
            $table->string('nama_checkpoint', 150);
            $table->text('deskripsi')->nullable();
            $table->enum('tipe', ['opening', 'closing', 'both'])->default('both');
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('all_cabang')->default(true);
            $table->timestamps();
        });

        // 2. Pivot: Cabang Khusus untuk Checkpoint
        Schema::create('checklist_checkpoint_cabang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checkpoint_id')->constrained('checklist_checkpoints')->cascadeOnDelete();
            $table->string('kode_cabang', 10);
            $table->timestamps();

            $table->index(['checkpoint_id', 'kode_cabang']);
        });

        // 3. Laporan Header
        Schema::create('checklist_laporan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_laporan', 50)->unique();
            $table->string('nik', 20)->index();
            $table->string('kode_cabang', 10)->index();
            $table->enum('tipe', ['opening', 'closing']);
            $table->date('tanggal');
            $table->time('jam');
            $table->string('latitude', 50)->nullable();
            $table->string('longitude', 50)->nullable();
            $table->string('lokasi_nama', 255)->nullable();
            $table->boolean('disclaimer_agreed')->default(true);
            $table->text('catatan')->nullable();
            $table->integer('total_checkpoint')->default(0);
            $table->timestamps();
        });

        // 4. Laporan Detail (Foto Satuan per Titik)
        Schema::create('checklist_laporan_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_id')->constrained('checklist_laporan')->cascadeOnDelete();
            $table->unsignedBigInteger('checkpoint_id')->nullable();
            $table->string('nama_checkpoint', 150);
            $table->text('deskripsi')->nullable();
            $table->string('foto');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 5. Laporan Collages (Foto Gabungan 2x2 per 4 titik untuk WA)
        Schema::create('checklist_laporan_collages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_id')->constrained('checklist_laporan')->cascadeOnDelete();
            $table->integer('batch_no')->default(1);
            $table->string('foto_collage');
            $table->integer('total_items')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_laporan_collages');
        Schema::dropIfExists('checklist_laporan_detail');
        Schema::dropIfExists('checklist_laporan');
        Schema::dropIfExists('checklist_checkpoint_cabang');
        Schema::dropIfExists('checklist_checkpoints');
    }
};
