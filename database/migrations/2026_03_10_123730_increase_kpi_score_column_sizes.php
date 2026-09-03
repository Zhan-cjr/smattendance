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
        Schema::table('kpi_details', function (Blueprint $table) {
            $table->decimal('skor', 12, 2)->change();
        });

        Schema::table('kpi_employees', function (Blueprint $table) {
            $table->decimal('total_nilai', 12, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kpi_details', function (Blueprint $table) {
            $table->decimal('skor', 5, 2)->change();
        });

        Schema::table('kpi_employees', function (Blueprint $table) {
            $table->decimal('total_nilai', 5, 2)->change();
        });
    }
};
