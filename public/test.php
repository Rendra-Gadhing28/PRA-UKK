<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$service = app(\App\Services\Booking\BeauticianAssignmentService::class);
$slots = $service->getDailySlotsAvailability(\Carbon\Carbon::parse('2026-09-01'), 45);
echo json_encode($slots, JSON_PRETTY_PRINT);
