<?php

namespace App\Livewire\Dashboard\Filters;

use Livewire\Component;

class TypeFilter extends Component
{
    public string $activeType;
    public string $activeView;

    public function mount($initialType, $initialView): void
    {
        $this->activeType = $initialType;
        $this->activeView = $initialView;
    }

    public function updatedActiveView(): void
    {
        $this->dispatch('viewUpdate', [
            'view' => $this->activeView
        ]);

        $this->dispatch('view-updated', $this->activeView);
    }

    public function updatedActiveType(): void
    {
        $this->dispatch('filtersUpdate', [
            'type' => $this->activeType
        ]);
    }

    public function render()
    {
        return view('livewire.dashboard.filters.type-filter');
    }
}
