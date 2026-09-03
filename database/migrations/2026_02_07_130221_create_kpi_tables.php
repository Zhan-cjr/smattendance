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
        // 1. KPI Periods
        Schema::create('kpi_periods', function (Blueprint $table) {
            $table->id();
            $table->string('nama_periode'); // e.g., "Januari 2024"
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(0);
            $table->timestamps();
        });

        // 2. KPI Indicators (Header)
        Schema::create('kpi_indicators', function (Blueprint $table) {
            $table->id();
            $table->string('kode_jabatan');
            $table->foreign('kode_jabatan')->references('kode_jabatan')->on('jabatan')->onDelete('cascade');
            $table->timestamps();
        });

        // 3. KPI Indicator Details (The List of Indicators)
        Schema::create('kpi_indicator_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kpi_indicator_id')->constrained('kpi_indicators')->onDelete('cascade');
            $table->string('nama_indikator');
            $table->text('deskripsi')->nullable();
            $table->string('satuan'); // e.g., "Rupiah", "Persen", "Skala"
            $table->enum('jenis_target', ['min', 'max']); // min: lower is better, max: higher is better
            $table->integer('bobot');
            $table->decimal('target', 12, 2)->default(0);
            $table->timestamps();
        });

        // 4. KPI Employees (Transaction Header)
        Schema::create('kpi_employees', function (Blueprint $table) {
            $table->id();
            $table->string('nik'); // FK to karyawan.nik
            $table->foreign('nik')->references('nik')->on('karyawan')->onDelete('cascade');
            $table->foreignId('kpi_period_id')->constrained('kpi_periods')->onDelete('cascade');
            $table->date('tanggal_penilaian');
            $table->decimal('total_nilai', 5, 2)->default(0);
            $table->string('grade')->nullable();
            $table->text('catatan_atasan')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved'])->default('draft');
            $table->timestamps();
        });

        // 5. KPI Details (Transaction Details)
        Schema::create('kpi_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kpi_employee_id')->constrained('kpi_employees')->onDelete('cascade');
            $table->foreignId('kpi_indicator_detail_id')->constrained('kpi_indicator_details')->onDelete('cascade');
            $table->decimal('target', 12, 2);
            $table->decimal('realisasi', 12, 2)->default(0);
            $table->integer('bobot');
            $table->decimal('skor', 5, 2)->default(0);
            $table->text('bukti_pendukung')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_details');
        Schema::dropIfExists('kpi_employees');
        Schema::dropIfExists('kpi_indicator_details');
        Schema::dropIfExists('kpi_indicators');
        Schema::dropIfExists('kpi_periods');
    }
};
