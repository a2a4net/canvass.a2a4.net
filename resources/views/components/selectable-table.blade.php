<div x-data="{ selectedId: @entangle('filters.eid') }">
    <table class="table table-sm table-hover">
        {{ $slot }}
    </table>
</div>
