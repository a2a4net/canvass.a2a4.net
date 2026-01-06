<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RunSchedule extends Command
{
    protected $signature = 'app:RunSchedule';
    protected $description = 'Симуляція обходу. Працює в будні';

    public function handle()
    {
        DB::connection()->disableQueryLog();

        $this->handleTasks();
    }

    public function handleTasks(?Carbon $timestamp = null): void
    {
        $timestamp ??= Carbon::now();

        $employees = DB::table('employees')
            ->where('is_active', true)
            ->select('id', 'name')
            ->get();

        foreach ($employees as $employee) {
            $baseProgress = $this->getDayProgress($timestamp);
            $userDeviation = rand(-20, 20) / 100;
            $progress = max(0, min(1, $baseProgress + $userDeviation));

            $stats = DB::table('tasks')
                ->where('employee_id', $employee->id)
                ->where('day', $timestamp->toDateString())
                ->selectRaw('COUNT(*) AS `total`, SUM(CASE WHEN `is_checked` = 1 THEN 1 ELSE 0 END) AS `checked`')
                ->first();

            if (!$stats || $stats->total == 0) continue;

            $targetCount = ceil($stats->total * $progress);

            if ($stats->checked < $targetCount/* && rand(1, 100) <= 75*/) {
                $task = DB::table('tasks')
                    ->where('employee_id', $employee->id)
                    ->where('day', $timestamp->toDateString())
                    ->where('is_checked', false)
                    ->orderBy('id')
                    ->first();

                if ($task) {
                    $this->markTask($task->id, $timestamp, $employee, 'План');
                }
            }

            if (rand(1, 100) <= 15) {
                $futureTask = DB::table('tasks')
                    ->where('employee_id', $employee->id)
                    ->where('day', '>=', $timestamp->copy()->startOfMonth()->toDateString())
                    ->where('day', '<=', $timestamp->copy()->endOfMonth()->toDateString())
                    ->where('is_checked', false)
                    ->inRandomOrder()
                    ->first();

                if ($futureTask) {
                    $this->markTask($futureTask->id, $timestamp, $employee, 'Позаплан');
                }
            }
        }
    }

    private function markTask($taskId, $timestamp, $employee, $type): void
    {
        $checkedAt = $timestamp->copy()->subSeconds(rand(0, 300));

        DB::table('tasks')->where('id', $taskId)->update([
            'is_checked' => true,
            'checked_at' => $checkedAt->toDateTimeString(),
        ]);

        $this->info($type . ' | ' . $employee->id . ' | ' . $employee->name . ' ' . $checkedAt->toDateTimeString());
    }

    private function getDayProgress(Carbon $now): float
    {
        $start = $now->copy()->setHour(8)->setMinute(0);
        $end = $now->copy()->setHour(18)->setMinute(0);

        if ($now <= $start) return 0;
        if ($now >= $end) return 1;

        return $start->diffInMinutes($now) / $start->diffInMinutes($end);
    }
}
