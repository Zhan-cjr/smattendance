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
        // 1. Add kode_dept to checklist_laporan
        if (Schema::hasTable('checklist_laporan') && !Schema::hasColumn('checklist_laporan', 'kode_dept')) {
            Schema::table('checklist_laporan', function (Blueprint $table) {
                $table->string('kode_dept', 10)->nullable()->after('kode_cabang')->index();
                $table->foreign('kode_dept')->references('kode_dept')->on('departemen')->nullOnDelete();
            });

            // Backfill existing reports from karyawan table
            DB::statement("UPDATE checklist_laporan l JOIN karyawan k ON l.nik = k.nik SET l.kode_dept = k.kode_dept WHERE l.kode_dept IS NULL");
        }

        // 2. Add all_dept to checklist_checkpoints
        if (Schema::hasTable('checklist_checkpoints') && !Schema::hasColumn('checklist_checkpoints', 'all_dept')) {
            Schema::table('checklist_checkpoints', function (Blueprint $table) {
                $table->boolean('all_dept')->default(true)->after('all_cabang');
            });
        }

        // 3. Create pivot checklist_checkpoint_departemen
        if (!Schema::hasTable('checklist_checkpoint_departemen')) {
            Schema::create('checklist_checkpoint_departemen', function (Blueprint $table) {
                $table->id();
                $table->foreignId('checkpoint_id')->constrained('checklist_checkpoints')->cascadeOnDelete();
                $table->string('kode_dept', 10);
                $table->timestamps();

                $table->foreign('kode_dept')->references('kode_dept')->on('departemen')->cascadeOnDelete();
                $table->index(['checkpoint_id', 'kode_dept']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('checklist_checkpoint_departemen')) {
            Schema::dropIfExists('checklist_checkpoint_departemen');
        }

        if (Schema::hasTable('checklist_checkpoints') && Schema::hasColumn('checklist_checkpoints', 'all_dept')) {
            Schema::table('checklist_checkpoints', function (Blueprint $table) {
                $table->dropColumn('all_dept');
            });
        }

        if (Schema::hasTable('checklist_laporan') && Schema::hasColumn('checklist_laporan', 'kode_dept')) {
            Schema::table('checklist_laporan', function (Blueprint $table) {
                $table->dropForeign(['kode_dept']);
                $table->dropColumn('kode_dept');
            });
        }
    }
};
