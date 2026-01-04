<div wire:ignore class="card">
    <div class="card-body p-0">
        <div x-on:view-updated.window="if ($event.detail[0] == 'map') {setTimeout(() => initMap(), 250);}" x-data="setupDashboardMap($refs.leafletMap, @js($geoJson))" x-init="if ($wire.filters.view === 'map') {setTimeout(() => initMap(), 250);}">
            <div x-ref="leafletMap" class="" style="height: 898px;"></div>
        </div>
    </div>
</div>
