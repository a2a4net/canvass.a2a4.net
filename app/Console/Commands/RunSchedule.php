<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Employee;

class RunSchedule extends Command
{
    protected $signature = 'app:RunSchedule';
    protected $description = 'Симуляція обходу. Працює в будні';

    public function handle()
    {
        $this->handleTasks(Carbon::create(2026, 1, 5, 9, 5));
    }

    public function handleTasks(?Carbon $timestamp = null): void
    {
        $timestamp ??= Carbon::now();

        $employees = Employee::isActive()
            ->orderBy('id')
            ->get();

        foreach ($employees as $employee) {
            $baseProgress = $this->getDayProgress($timestamp);
            $userDeviation = rand(-20, 20) / 100;
            $progress = max(0, min(1, $baseProgress + $userDeviation));

            $tasks = $employee->tasks()
                ->with(['employee', 'consumer'])
                ->whereDate('day', $timestamp->toDateString())
                ->orderBy('id')
                ->get();

            if ($tasks->isEmpty()) continue;

            $targetCount = ceil($tasks->count() * $progress);
            $currentChecked = $tasks->whereNotNull('checked_at')->count();

            if ($currentChecked < $targetCount && rand(1, 100) <= 60) {
                $task = $tasks->whereNull('checked_at')->first();

                if ($task) {
                    $randomTime = $timestamp->copy()->subSeconds(rand(0, 300));

                    $task->checkIn($randomTime);

                    $this->info($task->employee->id . ' | ' . $task->employee->name . ' ' . $task->consumer->address . ' ' . $randomTime->toDateTimeString());
                }
            }

            if (rand(1, 100) <= 15) {
                $futureTask = $employee->tasks()
                    ->isNotChecked()
                    ->whereDate('day', '>', $timestamp->toDateString())
                    ->orderBy('id')
                    ->first();

                if ($futureTask) {
                    $randomTime = $timestamp->copy()->subSeconds(rand(0, 300));

                    $futureTask->checkIn($randomTime);

                    $this->info($futureTask->employee->id . ' | ' . $futureTask->employee->name . ' ' . $futureTask->consumer->address . ' ' . $randomTime->toDateTimeString());
                }
            }
        }
    }

    private function getDayProgress(Carbon $now): float
    {
        $start = $now->copy()->setHour(8)->setMinute(0);
        $end = $now->copy()->setHour(18)->setMinute(0);

        if ($now <= $start) return 0;
        if ($now >= $end) return 1;

        $linear = $start->diffInMinutes($now) / $start->diffInMinutes($end);

        return sqrt($linear);
    }
}
