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
        Schema::table('karyawan', function (Blueprint $table) {
            // Tambah kolom nik_show setelah nik; nullable dulu untuk memudahkan backfill
            $table->string('nik_show', 30)->nullable()->after('nik');
        });

        // Backfill nilai awal: isi nik_show = nik yang sudah ada
        \Illuminate\Support\Facades\DB::table('karyawan')->update([
            'nik_show' => \Illuminate\Support\Facades\DB::raw('nik')
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $table->dropColumn('nik_show');
        });
    }
};
