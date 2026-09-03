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
        Schema::create('live_location', function (Blueprint $table) {
            $table->id();
            $table->string('nik')->index();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('lokasi')->nullable();
            $table->float('accuracy')->nullable();
            $table->dateTime('tracked_at')->nullable();
            $table->timestamps();

            // Foreign key
            $table->foreign('nik')
                ->references('nik')
                ->on('karyawan')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_location');
    }
};
