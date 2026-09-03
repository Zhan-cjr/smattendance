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
        // 1. Tambahkan kolom ke presensi_jamkerja jika belum ada
        Schema::table('presensi_jamkerja', function (Blueprint $table) {
            if (!Schema::hasColumn('presensi_jamkerja', 'istirahatlembur')) {
                $table->char('istirahatlembur', 1)->default('0')->after('jam_akhir_istirahat');
            }
            if (!Schema::hasColumn('presensi_jamkerja', 'jam_awal_istirahatlembur')) {
                $table->time('jam_awal_istirahatlembur')->nullable()->after('istirahatlembur');
            }
            if (!Schema::hasColumn('presensi_jamkerja', 'jam_akhir_istirahatlembur')) {
                $table->time('jam_akhir_istirahatlembur')->nullable()->after('jam_awal_istirahatlembur');
            }
        });

        // 2. Tambahkan kolom ke presensi jika belum ada
        Schema::table('presensi', function (Blueprint $table) {
            if (!Schema::hasColumn('presensi', 'istirahatlembur_in')) {
                $table->dateTime('istirahatlembur_in')->nullable()->after('foto_istirahat_out');
            }
            if (!Schema::hasColumn('presensi', 'lokasi_istirahatlembur_in')) {
                $table->string('lokasi_istirahatlembur_in')->nullable()->after('istirahatlembur_in');
            }
            if (!Schema::hasColumn('presensi', 'foto_istirahatlembur_in')) {
                $table->string('foto_istirahatlembur_in')->nullable()->after('lokasi_istirahatlembur_in');
            }
            if (!Schema::hasColumn('presensi', 'istirahatlembur_out')) {
                $table->dateTime('istirahatlembur_out')->nullable()->after('foto_istirahatlembur_in');
            }
            if (!Schema::hasColumn('presensi', 'lokasi_istirahatlembur_out')) {
                $table->string('lokasi_istirahatlembur_out')->nullable()->after('istirahatlembur_out');
            }
            if (!Schema::hasColumn('presensi', 'foto_istirahatlembur_out')) {
                $table->string('foto_istirahatlembur_out')->nullable()->after('lokasi_istirahatlembur_out');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presensi_jamkerja', function (Blueprint $table) {
            if (Schema::hasColumn('presensi_jamkerja', 'istirahatlembur')) {
                $table->dropColumn('istirahatlembur');
            }
            if (Schema::hasColumn('presensi_jamkerja', 'jam_awal_istirahatlembur')) {
                $table->dropColumn('jam_awal_istirahatlembur');
            }
            if (Schema::hasColumn('presensi_jamkerja', 'jam_akhir_istirahatlembur')) {
                $table->dropColumn('jam_akhir_istirahatlembur');
            }
        });

        Schema::table('presensi', function (Blueprint $table) {
            if (Schema::hasColumn('presensi', 'istirahatlembur_in')) {
                $table->dropColumn([
                    'istirahatlembur_in',
                    'lokasi_istirahatlembur_in',
                    'foto_istirahatlembur_in',
                    'istirahatlembur_out',
                    'lokasi_istirahatlembur_out',
                    'foto_istirahatlembur_out',
                ]);
            }
        });
    }
};
