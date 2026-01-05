<?php

namespace App\Livewire\Dashboard\Tables;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;

use App\Factories\AnalyticsFactory;

class TableView extends Component
{
    use WithPagination;

    public array $filters = [];
    private array $employees = [];
    protected $paginationTheme = 'bootstrap';

    #[On('tableUpdate')]
    public function applyFilters($filters): void
    {
        $this->filters = $filters;

        $this->resetPage();
    }

    public function selectEmployee(int $eid = null): void
    {
        $this->dispatch('showPoints', $eid);
    }

    public function render()
    {
        $service = AnalyticsFactory::make($this->filters['type']);

        return view('livewire.dashboard.tables.table-view', [
            'tableView' => $service->getTableView(),
            'employees' => $service->getData($this->filters)
        ]);
    }
}
