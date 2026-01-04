<?php

namespace App\Livewire\Dashboard\Map;

use Livewire\Component;
use Livewire\Attributes\On;

use App\Services\DataService;

class MapView extends Component
{
    public array $filters = [];

    #[On('mapUpdate')]
    public function applyFilters(array $filters): void
    {
        $this->filters = $filters;

        $this->dispatch('mapDataUpdated', $this->getGeoJson());
    }

    private function getGeoJson(): array
    {
        return app(DataService::class)->setFilters($this->filters)->getGeoJson();
    }

    public function render()
    {
        return view('livewire.dashboard.map.map-view', [
            'geoJson' => $this->getGeoJson(),
        ]);
    }
}
