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
        Schema::table('users_karyawan', function (Blueprint $table) {
            $table->unsignedBigInteger('approval_admin_id')->nullable()->after('id_user');
            $table->foreign('approval_admin_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_karyawan', function (Blueprint $table) {
            $table->dropForeign(['approval_admin_id']);
            $table->dropColumn('approval_admin_id');
        });
    }
};
