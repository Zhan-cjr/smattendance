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
        Schema::table('jenis_tunjangan', function (Blueprint $table) {
            $table->char('status_tunjangan', 1)->default('0')->after('jenis_tunjangan'); // 0: Tidak Tetap, 1: Tetap
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jenis_tunjangan', function (Blueprint $table) {
            $table->dropColumn('status_tunjangan');
        });
    }
};
