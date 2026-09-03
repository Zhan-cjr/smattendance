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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('pengirim'); // Nomor pengirim
            $table->string('penerima'); // Nomor penerima
            $table->text('pesan'); // Isi pesan
            $table->enum('status', ['success', 'failed'])->default('failed'); // Status pengiriman
            $table->string('message_id')->nullable(); // ID pesan dari API (jika berhasil)
            $table->text('error_message')->nullable(); // Pesan error (jika gagal)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
