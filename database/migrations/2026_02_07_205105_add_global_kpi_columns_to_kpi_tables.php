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
        // Drop foreign keys first to allow column modification
        Schema::table('kpi_indicators', function (Blueprint $table) {
            $table->dropForeign(['kode_jabatan']);
            $table->dropForeign(['kode_dept']);
        });

        // Now modify columns to be nullable
        Schema::table('kpi_indicators', function (Blueprint $table) {
            $table->string('kode_jabatan')->nullable()->change();
            $table->string('kode_dept')->nullable()->change();
        });

        // Re-add foreign keys
        Schema::table('kpi_indicators', function (Blueprint $table) {
            $table->foreign('kode_jabatan')->references('kode_jabatan')->on('jabatan')->onDelete('cascade');
            $table->foreign('kode_dept')->references('kode_dept')->on('departemen')->onDelete('cascade');
        });

        Schema::table('kpi_indicator_details', function (Blueprint $table) {
            $table->enum('mode', ['manual', 'auto'])->default('manual')->after('target');
            $table->string('metric_source')->nullable()->after('mode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kpi_indicator_details', function (Blueprint $table) {
            $table->dropColumn(['mode', 'metric_source']);
        });

        Schema::table('kpi_indicators', function (Blueprint $table) {
            $table->string('kode_jabatan')->nullable(false)->change();
            $table->string('kode_dept')->nullable(false)->change();
        });
    }
};
