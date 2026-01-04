<?php

namespace App\Livewire\Dashboard\Filters;

use Livewire\Component;

class EmployeeFilter extends Component
{
    public string $search = '';

    public function mount($initialSearch = ''): void
    {
        $this->search = $initialSearch ?? '';
    }

    public function updatedSearch(): void
    {
        $this->dispatch('filtersUpdate', [
            'search' => $this->search
        ]);
    }

    public function render()
    {
        return view('livewire.dashboard.filters.employee-filter');
    }
}
