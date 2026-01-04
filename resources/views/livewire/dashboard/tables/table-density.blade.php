<x-selectable-table>
    <thead>
        <tr>
            <th>{{ __('Контролер') }}</th>
            <th class="text-end">{{ __('Обійдено') }}</th>
            <th class="text-end">{{ __('Заплановано') }}</th>
            <th class="text-end">{{ __('Концентрація') }}</th>
            <th class="text-end">{{ __('Дисперсія') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($employees as $employee)
            <x-selectable-row :id="$employee->id">
                <td>{{ $employee->name }}</td>
                <td class="text-end">{{ $employee->total_checked }}</td>
                <td class="text-end">{{ $employee->total_planned }}</td>
                <td class="text-end">{{ $employee->concentration }}</td>
                <td class="text-end">{{ $employee->dispersion }} {{ __('м') }}</td>
            </x-selectable-row>
        @endforeach
    </tbody>
</x-selectable-table>
