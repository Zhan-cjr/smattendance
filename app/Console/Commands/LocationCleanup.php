<?php

namespace App\Console\Commands;

use App\Models\LiveLocation;
use Illuminate\Console\Command;

class LocationCleanup extends Command
{
    protected $signature = 'location:cleanup {--days=30}';
    protected $description = 'Delete old location tracking data older than specified days';

    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = now()->subDays($days);

        $deleted = LiveLocation::where('tracked_at', '<', $cutoffDate)->delete();

        $this->info("✓ Deleted {$deleted} old location records older than {$days} days");
        $this->line("Cutoff date: {$cutoffDate->format('Y-m-d H:i:s')}");
    }
}
