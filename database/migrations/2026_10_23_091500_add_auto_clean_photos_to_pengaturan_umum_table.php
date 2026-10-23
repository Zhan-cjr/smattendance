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
        Schema::table('pengaturan_umum', function (Blueprint $table) {
            $table->boolean('auto_hapus_foto')->default(false)->after('id')->comment('Toggle otomatis bersihkan file foto lama');
            $table->unsignedInteger('auto_hapus_foto_hari')->default(60)->after('auto_hapus_foto')->comment('Batas usia foto dalam hari (default 60 hari)');
            $table->string('auto_hapus_foto_jenis')->default('presensi,kunjungan,aktivitas,checklist')->after('auto_hapus_foto_hari')->comment('Jenis foto yang otomatis dibersihkan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaturan_umum', function (Blueprint $table) {
            $table->dropColumn(['auto_hapus_foto', 'auto_hapus_foto_hari', 'auto_hapus_foto_jenis']);
        });
    }
};
