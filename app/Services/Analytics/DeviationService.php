<?php

namespace App\Services\Analytics;

use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

use App\Models\AnalyticsProgress;
use App\Models\Employee;

class DeviationService
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
            ->selectRaw('ROUND(AVG(`deviation`), 2) as `avg_deviation`')
            ->whereBetween('day', [$from, $to])
            ->groupBy('employee_id');

        return Employee::isActive()
            ->select('employees.*')
            ->addSelect(DB::raw("COALESCE(`stats`.`total_p`, 0) AS `total_planned`, COALESCE(`stats`.`sum_cu`, 0) AS `total_checked_unplanned`, COALESCE(`stats`.`total_c`, 0) AS `total_checked`, COALESCE(`stats`.`avg_deviation`, 0) as `deviation`"))
            ->search($filters['search'] ?? null)
            ->leftJoinSub($analyticsQuery, 'stats', function ($join) {
                $join->on('employees.id', '=', 'stats.employee_id');
            })
            ->orderByDesc('deviation')
            ->orderByDesc('total_planned')
            ->orderByDesc('id')
            ->paginate(20)
            ->through(function ($employee) {
                $employee->deviation_human = number_format($employee->deviation, 2) . '%';

                return $employee;
            });
    }

    public function getTableView(): string
    {
        return 'table-deviation';
    }
}
