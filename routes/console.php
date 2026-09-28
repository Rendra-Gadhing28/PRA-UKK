<?php

use App\Console\Commands\ArchiveExpiredActivityLogs;
use App\Console\Commands\PurgeExpiredTrash;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jadwal harian otomatis pembersihan & pengarsipan
Schedule::command('logs:archive-expired')->daily()->at('00:05');
Schedule::command('trash:purge-expired')->daily()->at('00:10');
