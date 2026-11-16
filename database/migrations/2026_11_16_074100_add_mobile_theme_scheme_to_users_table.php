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
        if (!Schema::hasColumn('users', 'mobile_theme_scheme')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('mobile_theme_scheme', 20)->nullable()->after('remember_token');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'mobile_theme_scheme')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('mobile_theme_scheme');
            });
        }
    }
};
