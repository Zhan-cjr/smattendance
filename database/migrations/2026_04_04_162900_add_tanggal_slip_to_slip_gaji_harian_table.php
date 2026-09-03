<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('slip_gaji_harian', function (Blueprint $table) {
            $table->date('tanggal_slip')->nullable()->after('kode_slip_gaji_harian');
        });
    }

    public function down(): void
    {
        Schema::table('slip_gaji_harian', function (Blueprint $table) {
            $table->dropColumn('tanggal_slip');
        });
    }
};
