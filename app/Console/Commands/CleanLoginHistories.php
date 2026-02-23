<?php
// app/Console/Commands/CleanLoginHistories.php

namespace App\Console\Commands;

use App\Models\LoginHistory;
use Illuminate\Console\Command;

class CleanLoginHistories extends Command
{
    protected $signature = 'clean:login-histories {--days=30 : Number of days to keep}';
    protected $description = 'Delete old login history records';

    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = now()->subDays($days);

        $deleted = LoginHistory::where('login_at', '<', $cutoffDate)->delete();

        $this->info("Deleted {$deleted} old login history records.");
    }
}