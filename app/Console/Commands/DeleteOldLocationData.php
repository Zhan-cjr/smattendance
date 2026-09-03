<?php

namespace App\Console\Commands;

use App\Models\LiveLocation;
use Illuminate\Console\Command;

class DeleteOldLocationData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'location:delete-old {--days=7} {--dry-run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete location data older than specified days (default 7 days)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');

        $cutoffDate = now()->subDays($days);

        $query = LiveLocation::where('tracked_at', '<', $cutoffDate);

        $count = $query->count();

        if ($count === 0) {
            $this->info("No location data older than {$days} days found.");
            return 0;
        }

        $this->info("Found {$count} location records older than {$days} days (before {$cutoffDate->format('Y-m-d H:i:s')}).");

        if ($dryRun) {
            $this->warn('DRY RUN: No data will be deleted. Use without --dry-run to actually delete.');
            return 0;
        }

        if (!$this->confirm("Are you sure you want to delete {$count} records?")) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $deleted = $query->delete();

        $this->info("Successfully deleted {$deleted} location records.");

        return 0;
    }
}
