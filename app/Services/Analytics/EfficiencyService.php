<?php

namespace App\Services\Analytics;

use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

use App\Models\AnalyticsProgress;
use App\Models\Employee;

class EfficiencyService
{
    use WithPagination;

    public function getData(array $filters)
    {
        $from = $filters['date']['from'] ?? now()->toDateString();
        $to = $filters['date']['to'] ?? $from;

        $analyticsQuery = AnalyticsProgress::query()
            ->select('employee_id')
            ->selectRaw('SUM(`planned`) AS `total_p`')
            ->selectRaw('SUM(`checked_planned` + `checked_unplanned`) AS `total_c`')
            ->selectRaw('AVG(NULLIF(median_time, 0)) AS `avg_median`')
            ->whereBetween('day', [$from, $to])
            ->groupBy('employee_id');

        return Employee::isActive()
            ->select('employees.*')
            ->addSelect(DB::raw("COALESCE(`stats`.`total_p`, 0) AS `total_planned`, COALESCE(`stats`.`total_c`, 0) AS `total_checked`, IF(COALESCE(`stats`.`avg_median`, 0) > 0 AND COALESCE(`stats`.`total_p`, 0) > 0, ROUND(((`stats`.`total_c` / `stats`.`total_p`) * 100 / `stats`.`avg_median`) * 100, 2), 0) AS `efficiency_index`"))
            ->search($filters['search'] ?? null)
            ->leftJoinSub($analyticsQuery, 'stats', function ($join) {
                $join->on('employees.id', '=', 'stats.employee_id');
            })
            ->orderByRaw('efficiency_index = 0, efficiency_index DESC')
            ->orderByDesc('total_planned')
            ->paginate(20);
    }

    public function getTableView(): string
    {
        return 'table-efficiency';
    }
}
