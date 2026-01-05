<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;

class Dashboard extends Component
{
    #[Url(history: true)]
    public array $filters = [
        'eid' => '',
        'type' => 'progress',
        'view' => 'map',
        'search' => '',
        'date' => [
            'from' => '',
            'to' => ''
        ],
    ];

    protected function queryString(): array
    {
        return [
            'filters' => [
                'as' => 'f',
                'history' => true,
                'alwaysShow' => false,
            ],
        ];
    }

    protected function rules(): array
    {
        return [
            'filters.eid' => 'nullable|integer|exists:employees,id',
            'filters.view' => 'nullable|string|in:map,list',
            'filters.type' => 'required|string|in:progress,time,density,deviation',
            'filters.search' => 'nullable|string|max:100',
            'filters.date.from' => 'required|date|before_or_equal:today',
            'filters.date.to' => 'required|date|after_or_equal:filters.date.from|before_or_equal:today',
        ];
    }

    public function mount(): void
    {
        if (empty($this->filters['date']['from'])) {
            $this->filters['date']['from'] = now()->format('Y-m-d');
        }

        if (empty($this->filters['date']['to'])) {
            $this->filters['date']['to'] = now()->format('Y-m-d');
        }

        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->filters['type'] = 'progress';
            $this->filters['date']['from'] = now()->format('Y-m-d');
            $this->filters['date']['to'] = now()->format('Y-m-d');
        }
    }

    #[On('filtersUpdate')]
    public function updateFilters(array $filters): void
    {
        $this->filters = array_merge($this->filters, $filters);

        $this->validate();

        $this->dispatch('tableUpdate', $this->filters);

        $this->dispatchView();
    }

    #[On('viewUpdate')]
    public function updateView(array $filters): void
    {
        $this->filters = array_merge($this->filters, $filters);

        $this->validate();

        $this->dispatchView();
    }

    #[On('showPoints')]
    public function showPoints(int $eid = null): void
    {
        $this->filters['eid'] = $eid;

        $this->validate();

        $this->dispatchView();
    }

    private function dispatchView(): void
    {
        $this->dispatch($this->filters['view'] == 'map' ? 'mapUpdate' : 'listUpdate', $this->filters);
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
