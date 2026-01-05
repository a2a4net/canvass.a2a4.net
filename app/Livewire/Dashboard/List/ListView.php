<?php

namespace App\Livewire\Dashboard\List;

use App\Models\Employee;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;

use App\Services\DataService;

class ListView extends Component
{
    use WithPagination;

    private ?DataService $dataService = null;
    public array $filters = [];

    protected $paginationTheme = 'bootstrap';

    #[On('listUpdate')]
    public function applyFilters(array $filters): void
    {
        $this->filters = $filters;

        $this->resetPage('page-p');
    }

    private function service(): DataService
    {
        return $this->dataService ??= app(DataService::class)->setFilters($this->filters);
    }

    private function getPoints(): LengthAwarePaginator|null
    {
        return $this->service()->getPoints();
    }

    private function getEmployee(): Employee|null
    {
        return $this->service()->getEmployee();
    }

    public function render()
    {
        return view('livewire.dashboard.list.list-view', [
            'filterDate' => $this->filters['date']['from'] . (($this->filters['date']['from'] != $this->filters['date']['to'])  ? (' — ' . $this->filters['date']['to']) : ''),
            'employee' => $this->getEmployee(),
            'points' => $this->getPoints(),
        ]);
    }
}
