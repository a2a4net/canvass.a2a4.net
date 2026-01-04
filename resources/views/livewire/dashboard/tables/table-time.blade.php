<x-selectable-table>
    <thead>
        <tr>
            <th>{{ __('Контролер') }}</th>
            <th class="text-end">{{ __('Обійдено') }}</th>
            <th class="text-end">{{ __('Заплановано') }}</th>
            <th class="text-end">{{ __('Робочий день') }}</th>
            <th class="text-end">{{ __('Медіана') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($employees as $employee)
            <x-selectable-row :id="$employee->id">
                <td>{{ $employee->name }}</td>
                <td class="text-end">{{ $employee->total_checked ?? 0 }}</td>
                <td class="text-end">{{ $employee->total_planned ?? 0 }}</td>
                <td class="text-end">{{ $employee->work_time_human ?? '' }}</td>
                <td class="text-end">{{ $employee->median_time_human ?? '' }}</td>
            </x-selectable-row>
        @endforeach
    </tbody>
</x-selectable-table>
