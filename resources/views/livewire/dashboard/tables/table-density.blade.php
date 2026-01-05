<x-selectable-table>
    <thead>
        <tr>
            <th style="width: 48px;">{{ __('ID') }}</th>
            <th style="width: 220px;">{{ __('Контролер') }}</th>
            <th style="width: 85px;" class="text-end">{{ __('Виконано') }}</th>
            <th style="width: 85px;" class="text-end">{{ __('План') }}</th>
            <th class="text-end">{{ __('Концентрація') }} <i class="bi bi-info-circle" x-init="new bootstrap.Tooltip($el)" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="{{ __('Середнє в кластері 50х50м') }}"></i></th>
            <th class="text-end">{{ __('Дисперсія') }} <i class="bi bi-info-circle" x-init="new bootstrap.Tooltip($el)" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="{{ __('Відстань між кластерами') }}"></i></th>
        </tr>
    </thead>
    <tbody>
        @foreach($employees as $employee)
            <x-selectable-row :id="$employee->id">
                <td>{{ $employee->code }}</td>
                <td>{{ $employee->name }}</td>
                <td class="text-end">{{ $employee->total_checked }}</td>
                <td class="text-end">{{ $employee->total_planned }}</td>
                <td class="text-end">{{ $employee->concentration }}</td>
                <td class="text-end">{{ $employee->dispersion_human }}</td>
            </x-selectable-row>
        @endforeach
    </tbody>
</x-selectable-table>
