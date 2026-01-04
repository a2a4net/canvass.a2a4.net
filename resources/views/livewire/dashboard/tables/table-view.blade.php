<div class="card mt-3">
    <div class="card-header d-flex">
        <div class="flex-fill">
            <livewire:dashboard.filters.employee-filter :initialSearch="$filters['search']" />
        </div>
        <div class="ps-lg-2">
            <livewire:dashboard.filters.date-filter :initialDate="$filters['date']" />
        </div>
    </div>

    <div class="card-body">
        @if ($employees->isNotEmpty())
            @includeIf('livewire.dashboard.tables.' . $tableView)
        @else
            <div class="text-center">{{ __('Нічого не знайдено') }}</div>
        @endif
    </div>

    @if ($employees->hasPages())
        <div class="card-footer pt-2">
            {{ $employees->links('vendor.livewire.pagination-sm') }}
        </div>
    @endif
</div>
