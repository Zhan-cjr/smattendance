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
        Schema::table('mesin_fingerprints', function (Blueprint $table) {
            $table->string('titik_koordinat')->nullable()->after('lokasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mesin_fingerprints', function (Blueprint $table) {
            $table->dropColumn('titik_koordinat');
        });
    }
};
