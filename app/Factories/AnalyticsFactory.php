<?php

namespace App\Factories;

use App\Services\Analytics\ProgressService;
use App\Services\Analytics\TimeService;
use App\Services\Analytics\DensityService;
use App\Services\Analytics\DeviationService;

class AnalyticsFactory
{
    public static function make(string $type)
    {
        return match ($type) {
            'progress' => app(ProgressService::class),
            'time' => app(TimeService::class),
            'density' => app(DensityService::class),
            'deviation' => app(DeviationService::class),

            default => throw new \Exception('Unknown analytics type: ' . $type),
        };
    }
}
