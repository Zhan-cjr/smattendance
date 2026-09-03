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
        Schema::table('aktivitas_karyawan', function (Blueprint $table) {
            // Add jenis_aktivitas column if it doesn't exist
            if (!Schema::hasColumn('aktivitas_karyawan', 'jenis_aktivitas')) {
                $table->string('jenis_aktivitas')->nullable()->after('aktivitas');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aktivitas_karyawan', function (Blueprint $table) {
            if (Schema::hasColumn('aktivitas_karyawan', 'jenis_aktivitas')) {
                $table->dropColumn('jenis_aktivitas');
            }
        });
    }
};
