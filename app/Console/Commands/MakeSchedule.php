<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

use App\Models\Employee;
use App\Models\Consumer;

class MakeSchedule extends Command
{
    protected $signature = 'app:MakeSchedule {year} {month} {run?}';
    protected $description = 'Планування завдань для Працівників на місяць';
    private object $usedDates;

    public function handle()
    {
        $employees = Employee::isActive()
            ->orderBy('id')
            ->get();

        $monthStart = Carbon::create($this->argument('year'), $this->argument('month'));

        $period = CarbonPeriod::create($monthStart, '1 day', $monthStart->copy()->endOfMonth());

        $this->makeTasks($employees, $period);

        if ($this->argument('run')) {
            $this->runTasks($employees, $period);
        }
    }

    public function handleTasks(?Carbon $timestamp = null): void
    {
        $timestamp ??= Carbon::now();

        $employees = Employee::isActive()
            ->orderBy('id')
            ->get();

        foreach ($employees as $employee) {
            $tasks = $employee->tasks()
                ->with('employee')
                ->with('consumer')
                ->isNotChecked()
                ->whereDate('day', $timestamp->toDateString())
                ->orderBy('id')
                ->limit(rand(0, 3))
                ->get();

            foreach ($tasks as $task) {
                $task->checkIn($timestamp);

                $this->info($task->employee->id . ' | ' . $task->employee->name . ' ' . $task->consumer->address . ' ' . $timestamp->toDateTimeString());
            }
        }
    }

    private function runTasks($employees, $period): void
    {
        foreach ($employees as $employee) {
            foreach ($period as $date) {
                $tasks = $employee->tasks()
                    ->with('employee')
                    ->with('consumer')
                    ->isNotChecked()
                    ->whereDate('day', $date->toDateString())
                    ->orderBy('id')
                    ->get();

                $offTasks = $employee->tasks()
                    ->with('employee')
                    ->with('consumer')
                    ->isNotChecked()
                    ->whereDate('day', '>', $date->toDateString())
                    ->whereMonth('day', $date->month)
                    ->whereYear('day', $date->year)
                    ->orderBy('id')
                    ->limit(rand(0, 3))
                    ->get();

                $total = $tasks->count() + $offTasks->count();

                if ($total > 0) {
                    $this->usedDates = $this->generateRandomTimestamps($date, $total, rand(5, 8));

                    $this->checkIns($tasks);
                    $this->checkIns($offTasks);
                }
            }
        }
    }

    private function makeTasks($employees, $period): void
    {
        foreach ($employees as $employee) {
            foreach ($period as $date) {
                if ($date->isWeekend()) {
                    continue;
                }

                if (!$this->addTasks($employee, $date, rand(10, 50))) {
                    break;
                }
            }
        }
    }

    private function addTasks($employee, $date, $total): bool
    {
        $this->info($employee->name . ' ' . $date->toDateString() . ' (' . $total . ')');

        $consumers = Consumer::whereDoesntHave('tasks', function ($query) use ($date) {
            $query
                ->whereMonth('day', $date->month)
                ->whereYear('day', $date->year);
        })
            ->orderByRaw('FLOOR(lat * 1000), FLOOR(lon * 1000), lat, lon')
            #->orderBy('street')
            #->orderBy('housenumber')
            ->limit($total)
            ->cursor();

        if ($consumers->isEmpty()) {
            return false;
        }

        foreach ($consumers as $consumer) {
            $this->comment($consumer->address);

            $employee->tasks()->create([
                'day' => $date->toDateString(),
                'consumer_id' => $consumer->id,
                'is_checked' => false,
                'checked_at' => null,
            ]);
        }

        return true;
    }


    private function checkIns($tasks): void
    {
        foreach ($tasks as $task) {
            $randomDate = $this->usedDates->shift();

            $task->checkIn($randomDate);

            $this->info($task->employee->id . ' | ' . $task->employee->name . ' ' . $task->consumer->address . ' ' . $randomDate->toDateTimeString());
        }
    }

    private function generateRandomTimestamps(Carbon $date, int $count = 5, int $endHour = 18): Collection
    {
        $start = $date->clone()->hour(8)->minute(0)->second(0);

        $rangeSeconds = $start->timestamp - $date->clone()->hour($endHour)->minute(0)->second(0)->timestamp;

        return collect(range(1, $count))
            ->map(fn() => $start->clone()->addSeconds(rand(0, $rangeSeconds)))
            ->sort();
    }
}
