<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Models\Employee;

class DataService
{
    private array $filters = [];

    public function setFilters($filters): self
    {
        $this->filters = $filters;

        return $this;
    }

    public function getPoints(): LengthAwarePaginator|null
    {
        if (empty($this->filters['eid'])) {
            return null;
        }

        return $this->queryBuilder()
            ->orderByRaw('`day`, `is_checked` = 1, `checked_at`')
            ->paginate(32, pageName: 'page-p');
    }

    public function getGeoJson(): array
    {
        if (empty($this->filters['eid'])) {
            return [];
        }

        $filteredData = $this->queryBuilder()
            ->orderBy('checked_at')
            ->get();

        $coordinates = $filteredData->map(fn($p) => [
            (float)$p->consumer->lon,
            (float)$p->consumer->lat
        ])->values()->toArray();

        return [
            'type' => 'FeatureCollection',
            'features' => [
                [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'LineString',
                        'coordinates' => $coordinates,
                    ],
                    'properties' => ['type' => 'route']
                ],

                ...$filteredData->map(fn($p) => [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [(float)$p->consumer->lon, (float)$p->consumer->lat],
                    ],
                    'properties' => [
                        'id' => $p->consumer->id,
                        'address' => $p->consumer->address,
                        'checked_at' => $p->checked_at ? $p->checked_at->toDateTimeString() : null,
                        'planned' => ($p->checked_at && $p->checked_at->toDateString() == $p->day->toDateString())
                    ],
                ])->toArray()
            ]
        ];
    }

    private function queryBuilder()
    {
        return Employee::find($this->filters['eid'])->tasks()
            ->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->whereNotNull('checked_at')
                        ->whereBetween('checked_at', [
                            Carbon::parse($this->filters['date']['from'])->startOfDay(),
                            Carbon::parse($this->filters['date']['to'])->endOfDay()
                        ]);
                })
                    ->orWhereBetween('day', [$this->filters['date']['from'], $this->filters['date']['to']]);
            })
            ->with('consumer');
    }
}
