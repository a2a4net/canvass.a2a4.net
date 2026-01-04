<div class="row row-gap-3">
    <div class="col-lg-8 col-xl-7 col-xxl-6">
        <livewire:dashboard.filters.type-filter :initialType="$filters['type']" :initialView="$filters['view']" />
        <livewire:dashboard.tables.table-view :filters="$filters" />
    </div>

    <div class="col-lg-4 col-xl-5 col-xxl-6">
        <div x-show="$wire.filters.view == 'map'" x-cloak>
            <livewire:dashboard.map.map-view :filters="$filters" />
        </div>

        <div x-show="$wire.filters.view == 'list'" x-cloak>
            <livewire:dashboard.list.list-view :filters="$filters" />
        </div>
    </div>
</div>
