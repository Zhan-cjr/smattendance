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
        if (!Schema::hasColumn('kpi_indicators', 'kode_dept')) {
            Schema::table('kpi_indicators', function (Blueprint $table) {
                $table->char('kode_dept', 3)->after('kode_jabatan');
                $table->foreign('kode_dept')->references('kode_dept')->on('departemen')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kpi_indicators', function (Blueprint $table) {
            $table->dropForeign(['kode_dept']);
            $table->dropColumn('kode_dept');
        });
    }
};
