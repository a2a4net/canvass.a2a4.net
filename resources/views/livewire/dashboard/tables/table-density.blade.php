<x-selectable-table>
    <thead wire:key="{{ uniqid('wire-th') }}">
        <tr>
            <th style="width: 48px;">{{ __('ID') }}</th>
            <th style="width: 220px;">{{ __('Контролер') }}</th>
            <th style="width: 110px;" class="text-end">{{ __('Виконано') }} <x-info-icon :title="__('Всього обійдено точок (план + відхилення)')" /></th>
            <th style="width: 85px;" class="text-end">{{ __('План') }} <x-info-icon :title="__('Заплановано точок')" /></th>
            <th class="text-end">{{ __('Концентрація') }} <x-info-icon :title="__('Відсоток завдань, виконаних кучно')" /></th>
            <th class="text-end">{{ __('Дисперсія') }} <x-info-icon :title="__('Середня відстань від кожної точки до загального центру маршруту')" /></th>
        </tr>
    </thead>
    <tbody>
        @foreach($employees as $employee)
            <x-selectable-row :id="$employee->id">
                <td>{{ $employee->code }}</td>
                <td>{{ $employee->name }}</td>
                <td class="text-end">{{ $employee->total_checked }}</td>
                <td class="text-end">{{ $employee->total_planned }}</td>
                <td class="text-end">{{ $employee->concentration }}%</td>
                <td class="text-end">{{ $employee->dispersion_human }}</td>
            </x-selectable-row>
        @endforeach
    </tbody>
</x-selectable-table>
