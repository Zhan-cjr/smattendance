<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First convert existing data
        DB::table('kontrak')->where('jenis_kontrak', 'Kontrak')->update(['jenis_kontrak' => 'K']);
        DB::table('kontrak')->where('jenis_kontrak', 'Tetap')->update(['jenis_kontrak' => 'T']);

        Schema::table('kontrak', function (Blueprint $table) {
            $table->char('jenis_kontrak', 1)->default('K')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kontrak', function (Blueprint $table) {
             $table->string('jenis_kontrak', 20)->default('Kontrak')->change();
        });

        // Revert data
        DB::table('kontrak')->where('jenis_kontrak', 'K')->update(['jenis_kontrak' => 'Kontrak']);
        DB::table('kontrak')->where('jenis_kontrak', 'T')->update(['jenis_kontrak' => 'Tetap']);
    }
};
