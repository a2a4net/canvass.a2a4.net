<?php

namespace App\Services\Analytics;

use Carbon\CarbonInterval;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

use App\Models\Employee;
use App\Models\AnalyticsProgress;

class TimeService
{
    use WithPagination;

    public function getData(array $filters)
    {
        $from = $filters['date']['from'] ?? now()->toDateString();
        $to = $filters['date']['to'] ?? $from;

        $analyticsQuery = AnalyticsProgress::select('employee_id')
            ->selectRaw('SUM(`planned`) AS `total_p`')
            ->selectRaw('SUM(`checked_planned` + `checked_unplanned`) AS `total_c`')
            ->selectRaw('FLOOR(AVG(`median_time`)) AS `avg_median`')
            ->selectRaw('FLOOR(AVG(`work_time`)) AS `avg_work_time`')
            ->whereBetween('day', [$from, $to])
            ->groupBy('employee_id');

        return Employee::isActive()
            ->select('employees.*')
            ->addSelect(DB::raw("COALESCE(`stats`.`total_p`, 0) AS `total_planned`, COALESCE(`stats`.`total_c`, 0) AS `total_checked`, COALESCE(`stats`.`avg_median`, 0) AS `median_time`, COALESCE(`stats`.`avg_work_time`, 0) AS `work_time`, IF(COALESCE(`stats`.`total_p`, 0) > 0, ROUND((`stats`.`total_c` / `stats`.`total_p`) * 100, 2), 0) AS `total_progress`"))
            ->search($filters['search'] ?? null)
            ->leftJoinSub($analyticsQuery, 'stats', function ($join) {
                $join->on('employees.id', '=', 'stats.employee_id');
            })
            ->orderByRaw('`median_time` = 0, `median_time` ASC')
            ->orderByDesc('total_planned')
            ->orderByDesc('id')
            ->paginate(20)
            ->through(function ($employee) {
                $employee->median_time_human = CarbonInterval::seconds($employee->median_time)->cascade()->forHumans(['short' => true]);
                $employee->work_time_human = CarbonInterval::seconds($employee->work_time)->cascade()->forHumans(['short' => true]);

                return $employee;
            });
    }

    public function getTableView(): string
    {
        return 'table-time';
    }
}
