<?php

namespace App\Console\Commands;

use App\Services\ActivityLogger;
use Illuminate\Console\Command;

class PurgeExpiredTrash extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trash:purge-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatis menghapus permanen (force delete) data soft-deleted yang lebih dari 30 hari';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Memulai pembersihan data tong sampah (soft deletes) > 30 hari...');
        $purged = ActivityLogger::purgeExpiredTrash();

        foreach ($purged as $model => $count) {
            $this->line("- {$model}: {$count} data dibersihkan secara permanen.");
        }

        $total = array_sum($purged);
        $this->info("Total data dibersihkan: {$total}");

        return self::SUCCESS;
    }
}
