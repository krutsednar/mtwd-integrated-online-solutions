<?php

namespace App\Console\Commands;

use App\Jobs\SyncOnlineAttendance as SyncJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncOnlineAttendance extends Command
{
    protected $signature   = 'hris:sync-attendance
                                {--date-from= : Only fetch records on or after this date (YYYY-MM-DD)}
                                {--date-to=   : Only fetch records on or before this date (YYYY-MM-DD)}';

    protected $description = 'Fetch unsynced attendance records from the Online HRIS and save them locally.';

    public function handle(): int
    {
        $this->info('Starting Online HRIS attendance sync...');

        $job = new SyncJob();
        $job->handle();

        $this->info('Sync complete. Check laravel.log for details.');

        return Command::SUCCESS;
    }
}
