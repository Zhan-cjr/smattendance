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
        Schema::table('presensi', function (Blueprint $table) {
            $table->unsignedBigInteger('id_mesin')->nullable()->after('foto_out');
            $table->foreign('id_mesin')->references('id')->on('mesin_fingerprints')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presensi', function (Blueprint $table) {
            $table->dropForeign(['id_mesin']);
            $table->dropColumn('id_mesin');
        });
    }
};
