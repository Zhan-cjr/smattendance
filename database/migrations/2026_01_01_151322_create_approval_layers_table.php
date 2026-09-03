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
        Schema::create('approval_layers', function (Blueprint $table) {
            $table->id();
            $table->string('feature'); // e.g., IZIN_ABSEN, CUTI
            $table->integer('level'); // 1, 2, 3
            $table->string('role_name'); // e.g., hrd, manager
            $table->char('kode_dept', 3)->nullable(); // Optional specific dept
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_layers');
    }
};
