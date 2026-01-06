<?php

namespace App\Services\Analytics;

use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

use App\Models\AnalyticsProgress;
use App\Models\Employee;

class DensityService
{
    use WithPagination;

    public function getData(array $filters)
    {
        $from = $filters['date']['from'] ?? now()->toDateString();
        $to = $filters['date']['to'] ?? $from;

        $analyticsQuery = AnalyticsProgress::select('employee_id')
            ->selectRaw('SUM(`planned`) AS `total_p`')
            ->selectRaw('SUM(`checked_planned` + `checked_unplanned`) AS `total_c`')
            ->selectRaw('FLOOR(AVG(`dispersion`)) AS `avg_dispersion`')
            ->selectRaw('ROUND(AVG(`concentration`)) AS `avg_concentration`')
            ->whereBetween('day', [$from, $to])
            ->groupBy('employee_id');

        return Employee::isActive()
            ->selectRaw('`employees`.*')
            ->selectRaw('COALESCE(`stats`.`total_p`, 0) AS `total_planned`')
            ->selectRaw('COALESCE(`stats`.`total_c`, 0) AS `total_checked`')
            ->selectRaw('COALESCE(`stats`.`avg_dispersion`, 0) AS `dispersion`')
            ->selectRaw('COALESCE(`stats`.`avg_concentration`, 0) AS `concentration`')
            ->search($filters['search'] ?? null)
            ->leftJoinSub($analyticsQuery, 'stats', function ($join) {
                $join->on('employees.id', '=', 'stats.employee_id');
            })
            ->orderByRaw('`dispersion` = 0, `dispersion` ASC')
            ->orderByDesc('total_planned')
            ->orderByDesc('id')
            ->paginate(20)
            ->through(function ($employee) {
                $employee->dispersion_human = $employee->dispersion >= 1000 ? (number_format(round($employee->dispersion / 1000), 0, '.', ' ') . ' км') : ($employee->dispersion . ' м');

                return $employee;
            });
    }

    public function getTableView(): string
    {
        return 'table-density';
    }
}
