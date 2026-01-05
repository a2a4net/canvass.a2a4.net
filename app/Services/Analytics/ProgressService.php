<?php

namespace App\Services\Analytics;

use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

use App\Models\AnalyticsProgress;
use App\Models\Employee;

class ProgressService
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
            ->whereBetween('day', [$from, $to])
            ->groupBy('employee_id');

        return Employee::isActive()
            ->select('employees.*')
            ->addSelect(DB::raw("COALESCE(`stats`.`total_p`, 0) AS `total_planned`, COALESCE(`stats`.`sum_cu`, 0) AS `total_checked_unplanned`, COALESCE(`stats`.`total_c`, 0) AS `total_checked`, IF(COALESCE(`stats`.`total_p`, 0) > 0, ROUND((`stats`.`total_c` / `stats`.`total_p`) * 100, 2), 0) AS `total_progress`"))
            ->search($filters['search'] ?? null)
            ->leftJoinSub($analyticsQuery, 'stats', function ($join) {
                $join->on('employees.id', '=', 'stats.employee_id');
            })
            ->orderByDesc('total_progress')
            ->orderByDesc('total_planned')
            ->paginate(20);
    }

    public function getTableView(): string
    {
        return 'table-progress';
    }
}
