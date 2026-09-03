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
        Schema::table('approval_layers', function (Blueprint $table) {
            $table->char('kode_jabatan', 3)->nullable()->after('kode_dept');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('approval_layers', function (Blueprint $table) {
            $table->dropColumn('kode_jabatan');
        });
    }
};
