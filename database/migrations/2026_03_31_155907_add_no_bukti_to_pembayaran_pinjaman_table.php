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
        Schema::table('pembayaran_pinjaman', function (Blueprint $blueprint) {
            $blueprint->string('no_bukti', 20)->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_pinjaman', function (Blueprint $blueprint) {
            $blueprint->dropColumn('no_bukti');
        });
    }
};
