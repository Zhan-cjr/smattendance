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
        Schema::create('update_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('version'); // Versi yang diupdate
            $table->string('previous_version')->nullable(); // Versi sebelumnya
            $table->enum('status', ['pending', 'downloading', 'installing', 'success', 'failed'])->default('pending');
            $table->text('message')->nullable(); // Pesan error atau success
            $table->text('error_log')->nullable(); // Log error detail
            $table->timestamp('started_at')->nullable(); // Waktu mulai update
            $table->timestamp('completed_at')->nullable(); // Waktu selesai update
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('update_logs');
    }
};
