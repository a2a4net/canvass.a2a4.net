<x-selectable-table>
    <thead wire:key="{{ uniqid('wire-th') }}">
        <tr>
            <th style="width: 48px;">{{ __('ID') }}</th>
            <th style="width: 220px;">{{ __('Контролер') }}</th>
            <th style="width: 110px;" class="text-end">{{ __('Виконано') }} <x-info-icon :title="__('Всього обійдено точок (план + відхилення)')" /></th>
            <th style="width: 85px;" class="text-end">{{ __('План') }} <x-info-icon :title="__('Заплановано точок')" /></th>
            <th class="text-end">{{ __('Роб. день') }} <x-info-icon :title="__('Тривалість робочого дня')" /></th>
            <th class="text-end">{{ __('Медіана') }} <x-info-icon :title="__('Час на одне завдання (медіанне значення)')" /></th>
            <th class="text-end">{{ __('Продуктивність') }} <x-info-icon :title="__('Реальна швидкість роботи відносно медіанної, з урахуванням виконання плану')" /></th>
        </tr>
    </thead>
    <tbody>
        @foreach($employees as $employee)
            <x-selectable-row :id="$employee->id">
                <td>{{ $employee->code }}</td>
                <td>{{ $employee->name }}</td>
                <td class="text-end">{{ $employee->total_checked ?? 0 }}</td>
                <td class="text-end">{{ $employee->total_planned ?? 0 }}</td>
                <td class="text-end">{{ $employee->work_time_human ?? '' }}</td>
                <td class="text-end">{{ $employee->median_time_human ?? '' }}</td>
                <td class="text-end">{{ $employee->efficiency_index_human }}%</td>
            </x-selectable-row>
        @endforeach
    </tbody>
</x-selectable-table>
