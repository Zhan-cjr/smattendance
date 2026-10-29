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
            if (!Schema::hasColumn('pengaturan_umum', 'nama_aplikasi')) {
                $table->string('nama_aplikasi')->nullable()->after('nama_perusahaan');
            }
            if (!Schema::hasColumn('pengaturan_umum', 'admin_theme')) {
                $table->string('admin_theme', 20)->default('v2')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaturan_umum', function (Blueprint $table) {
            if (Schema::hasColumn('pengaturan_umum', 'admin_theme')) {
                $table->dropColumn('admin_theme');
            }
            if (Schema::hasColumn('pengaturan_umum', 'nama_aplikasi')) {
                $table->dropColumn('nama_aplikasi');
            }
        });
    }
};
