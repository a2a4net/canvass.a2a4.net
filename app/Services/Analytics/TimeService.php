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
            ->selectRaw('SUM(`checked_unplanned`) AS `sum_cu`')
            ->selectRaw('FLOOR(AVG(`median_time`)) AS `avg_median`')
            ->selectRaw('FLOOR(AVG(`work_time`)) AS `avg_work_time`')
            ->whereBetween('day', [$from, $to])
            ->groupBy('employee_id');

        return Employee::isActive()
            ->selectRaw('`employees`.*')
            ->selectRaw('COALESCE(`stats`.`total_p`, 0) AS `total_planned`')
            ->selectRaw('COALESCE(`stats`.`total_c`, 0) AS `total_checked`')
            ->selectRaw('COALESCE(`stats`.`avg_median`, 0) AS `median_time`')
            ->selectRaw('COALESCE(`stats`.`avg_work_time`, 0) AS `work_time`')
            ->selectRaw('IF(COALESCE(`stats`.`total_p`, 0) > 0, ROUND((`stats`.`total_c` / `stats`.`total_p`) * 100, 2), 0) AS `total_progress`')
            ->selectRaw('IF(COALESCE(`stats`.`total_c`, 0) > 0 AND COALESCE(`stats`.`avg_work_time`, 0) > 0 AND COALESCE(`stats`.`avg_median`, 0) > 0 AND COALESCE(`stats`.`total_p`, 0) > 0, LEAST(300, ROUND(( (`stats`.`avg_median` / (`stats`.`avg_work_time` / `stats`.`total_c`)) * 100) * (`stats`.`total_c` / `stats`.`total_p`), 2)), 0) AS `efficiency_index`')
            ->search($filters['search'] ?? null)
            ->leftJoinSub($analyticsQuery, 'stats', function ($join) {
                $join->on('employees.id', '=', 'stats.employee_id');
            })
            ->orderByDesc('efficiency_index')
            ->orderByRaw('`median_time` = 0, `median_time` ASC')
            ->orderByDesc('total_planned')
            ->orderByDesc('id')
            ->paginate(20)
            ->through(function ($employee) {
                $employee->median_time_human = CarbonInterval::seconds($employee->median_time)->cascade()->forHumans(['short' => true, 'parts' => 2]);
                $employee->work_time_human = CarbonInterval::seconds($employee->work_time)->cascade()->forHumans(['short' => true, 'parts' => 2]);
                $employee->efficiency_index_human = number_format($employee->efficiency_index, 2, '.', '');

                return $employee;
            });
    }

    public function getTableView(): string
    {
        return 'table-time';
    }
}
