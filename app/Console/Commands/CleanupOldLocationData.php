<?php

namespace App\Console\Commands;

use App\Models\LiveLocation;
use Illuminate\Console\Command;

class CleanupOldLocationData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'location:cleanup {--days=7 : Number of days to keep}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup old live location tracking data older than specified days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = now()->subDays($days);

        $deleted = LiveLocation::where('tracked_at', '<', $cutoffDate)->delete();

        $this->info("Cleanup completed. Deleted {$deleted} old location records (older than {$days} days).");
        
        return Command::SUCCESS;
    }
}
