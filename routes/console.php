<?php

use Illuminate\Support\Facades\Schedule;

use App\Console\Commands\RunSchedule;
use App\Console\Commands\CalculateAnalyticsProgress;

Schedule::command(RunSchedule::class)
    ->weekdays()
    ->between('09:05', '18:00')
    ->everyFiveMinutes();

Schedule::command(CalculateAnalyticsProgress::class, [now()->subDay()->toDateString()])
    ->daily();

Schedule::command(CalculateAnalyticsProgress::class, [now()->toDateString()])
    ->weekdays()
    ->between('09:10', '18:05')
    ->everyFiveMinutes();
