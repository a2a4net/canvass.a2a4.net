@props(['id'])

<tr role="button" wire:key="emp-{{ $id }}" x-on:click="selectedId = {{ $id }}; $wire.selectEmployee({{ $id }})" :class="selectedId == {{ $id }} ? 'table-active' : ''" {{ $attributes }}>
    {{ $slot }}
</tr>
