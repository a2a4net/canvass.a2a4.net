<x-selectable-table>
    <thead>
        <tr>
            <th>{{ __('Контролер') }}</th>
            <th class="text-end">{{ __('Обійдено') }}</th>
            <th class="text-end">{{ __('Заплановано') }}</th>
            <th class="text-end">{{ __('Продуктивність') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($employees as $employee)
            <x-selectable-row :id="$employee->id">
                <td>{{ $employee->name }}</td>
                <td class="text-end">{{ $employee->total_checked ?? 0 }}</td>
                <td class="text-end">{{ $employee->total_planned ?? 0 }}</td>
                <td class="text-end">{{ $employee->efficiency_index }}</td>
            </x-selectable-row>
        @endforeach
    </tbody>
</x-selectable-table>
