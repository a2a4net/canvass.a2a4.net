<x-selectable-table>
    <thead>
        <tr>
            <th style="width: 48px;">{{ __('ID') }}</th>
            <th style="width: 220px;">{{ __('Контролер') }}</th>
            <th style="width: 85px;" class="text-end">{{ __('Виконано') }}</th>
            <th style="width: 85px;" class="text-end">{{ __('План') }}</th>
            <th class="text-end">{{ __('Продуктивність') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($employees as $employee)
            <x-selectable-row :id="$employee->id">
                <td>{{ $employee->code }}</td>
                <td>{{ $employee->name }}</td>
                <td class="text-end">{{ $employee->total_checked ?? 0 }}</td>
                <td class="text-end">{{ $employee->total_planned ?? 0 }}</td>
                <td class="text-end">{{ $employee->efficiency_index }}</td>
            </x-selectable-row>
        @endforeach
    </tbody>
</x-selectable-table>
