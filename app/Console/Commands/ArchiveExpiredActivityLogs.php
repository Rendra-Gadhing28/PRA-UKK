<?php

namespace App\Console\Commands;

use App\Services\ActivityLogger;
use Illuminate\Console\Command;

class ArchiveExpiredActivityLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:archive-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatis mengarsipkan activity logs yang sudah melewati 30 hari';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Memulai pengarsipan activity logs > 30 hari...');
        $count = ActivityLogger::archiveExpiredLogs();
        $this->info("Berhasil mengarsipkan {$count} activity log.");

        return self::SUCCESS;
    }
}
