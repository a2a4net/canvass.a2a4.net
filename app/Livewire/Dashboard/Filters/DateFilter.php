<?php

namespace App\Livewire\Dashboard\Filters;

use Livewire\Component;
use Livewire\Attributes\On;

class DateFilter extends Component
{
    public array $range = [];

    public function mount($initialDate): void
    {
        $this->range = $initialDate;
    }

    #[On('dateRangeSelected')]
    public function setRange(array $range): void
    {
        $this->dispatch('filtersUpdate', [
            'date' => $range
        ]);
    }

    public function render()
    {
        return view('livewire.dashboard.filters.date-filter');
    }
}
