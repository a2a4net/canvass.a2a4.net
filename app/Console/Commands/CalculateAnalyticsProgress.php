<?php

namespace App\Console\Commands;

use App\Models\Employee;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Console\Command;

class CalculateAnalyticsProgress extends Command
{
    protected $signature = 'app:CalculateAnalyticsProgress {from} {to?}';
    protected $description = 'Розрахунок прогресу працівників';

    public function handle()
    {
        $from = Carbon::parse($this->argument('from'));

        $to = $this->argument('to') ? Carbon::parse($this->argument('to')) : $from->copy();

        $period = CarbonPeriod::create($from, $to);

        $this->info("Період: {$from->toDateString()} - {$to->toDateString()}");

        foreach ($period as $date) {
            $this->calculate($date);
        }
    }

    private function calculate($date): void
    {
        $employees = Employee::isActive()
            ->orderBy('id')
            ->get();

        foreach ($employees as $employee) {
            $diffTimes = [];
            $prevTime = null;

            $fields = [
                'median_time' => 0,
                'work_time' => 0,
                'dispersion' => 0,
                'concentration' => 0,
                'deviation' => 0
            ];

            $density = $employee->tasks()
                ->join('consumers', 'tasks.consumer_id', '=', 'consumers.id')
                ->whereDate('tasks.checked_at', $date->toDateString())
                ->selectRaw('FLOOR(`consumers`.`lat` / 0.0005) * 0.0005 AS `lat_grid`, FLOOR(`consumers`.`lon` / 0.0005) * 0.0005 AS `lon_grid`, COUNT(*) AS `weight`')
                ->groupBy('lat_grid', 'lon_grid')
                ->get();

            if ($density->isNotEmpty()) {
                $avgLat = $density->avg('lat_grid');
                $avgLon = $density->avg('lon_grid');

                $fields['dispersion'] = (int)round($density->map(function ($item) use ($avgLat, $avgLon) {
                    return sqrt(pow(($item->lat_grid - $avgLat) * 111000, 2) + pow(($item->lon_grid - $avgLon) * 74000, 2));
                })->avg());

                $fields['concentration'] = (int)round(($density->where('weight', '>', 1)->sum('weight') / $density->sum('weight')) * 100);
            }

            $fields['planned'] = $employee->tasks()
                ->whereDate('day', $date->toDateString())
                ->count();

            $fields['checked_planned'] = $employee->tasks()
                ->isChecked()
                ->whereDate('day', $date->toDateString())
                ->whereBetween('checked_at', [$date->startOfDay()->toDateTimeString(), $date->endOfDay()->toDateTimeString()])
                ->count();

            $fields['checked_unplanned'] = $employee->tasks()
                ->isChecked()
                ->whereDate('day', '<>', $date->toDateString())
                ->whereBetween('checked_at', [$date->startOfDay()->toDateTimeString(), $date->endOfDay()->toDateTimeString()])
                ->count();

            if ($fields['checked_planned'] > 0 || $fields['checked_unplanned'] > 0) {
                $fields['deviation'] = round(($fields['checked_unplanned'] / ($fields['checked_planned'] + $fields['checked_unplanned'])) * 100, 2);
            }

            $tasks = $employee->tasks()
                ->isChecked()
                ->whereBetween('checked_at', [$date->startOfDay()->toDateTimeString(), $date->endOfDay()->toDateTimeString()])
                ->orderBy('checked_at')
                ->get();

            foreach ($tasks as $task) {
                if ($prevTime !== null) {
                    $diff = $prevTime->diffInSeconds($task->checked_at);

                    if ($diff > 0) {
                        $diffTimes[] = $diff;
                    }
                }

                $prevTime = $task->checked_at;
            }

            sort($diffTimes);

            $count = count($diffTimes);

            if ($count > 2) {
                $mid = floor($count / 2);

                $fields['median_time'] = ($count % 2 === 0) ? ($diffTimes[$mid - 1] + $diffTimes[$mid]) / 2 : $diffTimes[$mid];

                $startTime = $tasks->whereNotNull('checked_at')->sortBy('checked_at')->first()->checked_at;
                $endTime = $tasks->whereNotNull('checked_at')->sortBy('checked_at')->last()->checked_at;

                if ($startTime && $endTime) {
                    $fields['work_time'] = abs($startTime->diffInSeconds($endTime));
                }
            }

            $fields['total_progress'] = number_format($fields['planned'] ? (($fields['checked_planned'] + $fields['checked_unplanned']) / $fields['planned']) * 100 : 0, 2, '.', '');

            $employee->analyticsProgress()->updateOrInsert(['employee_id' => $employee->id, 'day' => $date->toDateString()], $fields);
        }

        $this->info('Аналітику за ' . $date->toDateString() . ' успішно оновлено');
    }
}
