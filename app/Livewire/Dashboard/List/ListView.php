<?php

namespace App\Livewire\Dashboard\List;

use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;

use App\Services\DataService;

class ListView extends Component
{
    use WithPagination;

    public array $filters = [];

    protected $paginationTheme = 'bootstrap';

    #[On('listUpdate')]
    public function applyFilters(array $filters): void
    {
        $this->filters = $filters;

        $this->resetPage('page-p');
    }

    private function getPoints(): LengthAwarePaginator|null
    {
        return app(DataService::class)->setFilters($this->filters)->getPoints();
    }

    public function render()
    {
        return view('livewire.dashboard.list.list-view', [
            'points' => $this->getPoints(),
        ]);
    }
}
