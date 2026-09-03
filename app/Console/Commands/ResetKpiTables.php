<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResetKpiTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kpi:reset-tables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop all KPI tables to allow fresh migration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Dropping KPI tables...");

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        $tables = [
            'kpi_details',
            'kpi_employees',
            'kpi_jabatan_indicators',
            'kpi_indicator_details', // Potential future table
            'kpi_indicators',
            'kpi_periods'
        ];

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
            $this->info("Dropped table: $table");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info("All KPI tables dropped successfully.");
        return 0;
    }
}
