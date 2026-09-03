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
        Schema::table('slip_gaji', function (Blueprint $table) {
            $table->enum('jenis_upah', ['Bulanan', 'Harian'])->default('Bulanan')->after('tahun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('slip_gaji', function (Blueprint $table) {
            $table->dropColumn('jenis_upah');
        });
    }
};
