<x-selectable-table>
    <thead wire:key="{{ uniqid('wire-th') }}">
        <tr>
            <th style="width: 48px;">{{ __('ID') }}</th>
            <th style="width: 220px;">{{ __('Контролер') }}</th>
            <th style="width: 110px;" class="text-end">{{ __('Виконано') }} <x-info-icon :title="__('Всього обійдено точок (план + відхилення)')" /></th>
            <th style="width: 85px;" class="text-end">{{ __('План') }} <x-info-icon :title="__('Заплановано точок')" /></th>
            <th class="text-end">{{ __('Поза маршрутом') }}</th>
            <th class="text-end">{{ __('Відхилення') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($employees as $employee)
            <x-selectable-row :id="$employee->id">
                <td>{{ $employee->code }}</td>
                <td>{{ $employee->name }}</td>
                <td class="text-end">{{ $employee->total_checked }}</td>
                <td class="text-end">{{ $employee->total_planned }}</td>
                <td class="text-end">
                    @if ($employee->total_checked_unplanned)
                        <span class="badge rounded-pill text-bg-info px-3 text-white">{{ $employee->total_checked_unplanned }}</span>
                    @endif
                </td>
                <td class="text-end">{{ $employee->deviation_human }}</td>
            </x-selectable-row>
        @endforeach
    </tbody>
</x-selectable-table>
