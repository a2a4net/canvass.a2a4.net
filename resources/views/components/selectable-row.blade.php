@props(['id'])

<tr role="button" wire:key="emp-{{ $id }}" x-on:click="let newId = (selectedId == {{ $id }}) ? null : {{ $id }}; selectedId = newId; $wire.selectEmployee(newId)" :class="selectedId == {{ $id }} ? 'table-active' : ''" {{ $attributes }}>
    {{ $slot }}
</tr>
