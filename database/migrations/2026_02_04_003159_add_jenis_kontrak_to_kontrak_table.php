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
        Schema::table('kontrak', function (Blueprint $table) {
            $table->string('jenis_kontrak', 20)->default('Kontrak')->after('no_kontrak');
            $table->date('dari')->nullable()->change();
            $table->date('sampai')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kontrak', function (Blueprint $table) {
            $table->dropColumn('jenis_kontrak');
            $table->date('dari')->nullable(false)->change();
            $table->date('sampai')->nullable(false)->change();
        });
    }
};
