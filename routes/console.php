<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\GenerateWeeklyTribeFoodRecommendations;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jalan tiap Senin jam 00:00
Schedule::job(new GenerateWeeklyTribeFoodRecommendations)
    ->weeklyOn(1, '00:00')
    ->withoutOverlapping();
