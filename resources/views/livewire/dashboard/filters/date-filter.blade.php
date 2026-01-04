<div wire:ignore x-data x-init="initDateRangePicker($refs.dateRangeinput)" @date-range-selected.window="$wire.dispatch('dateRangeSelected', {range: $event.detail})" style="width: 230px;">
    <input x-ref="dateRangeinput" type="text" class="form-control text-center px-3 w-full" data-from="{{ $range['from'] }}" data-to="{{ $range['to'] }}" placeholder="Оберіть період">
</div>
