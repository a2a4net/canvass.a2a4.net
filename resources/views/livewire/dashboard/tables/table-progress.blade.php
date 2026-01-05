<x-selectable-table>
    <thead>
        <tr>
            <th style="width: 48px;">{{ __('ID') }}</th>
            <th style="width: 220px;">{{ __('Контролер') }}</th>
            <th style="width: 85px;" class="text-end">{{ __('Виконано') }}</th>
            <th style="width: 85px;" class="text-end">{{ __('План') }}</th>
            <th style="width: 135px;" class="text-end">{{ __('Відхилення') }} <i class="bi bi-info-circle" x-init="new bootstrap.Tooltip($el)" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="{{ __('Точок поза плановим маршрутом') }}"></i></th>
            <th class="text-center">{{ __('Виконання') }}</th>
            <th class="text-end" style="width: 50px;">{{ __('%') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($employees as $employee)
            <x-selectable-row :id="$employee->id">
                <td>{{ $employee->code }}</td>
                <td>{{ $employee->name }}</td>
                <td class="text-end">{{ $employee->total_checked ?? 0 }}</td>
                <td class="text-end">{{ $employee->total_planned ?? 0 }}</td>
                <td class="text-end">
                    @if ($employee->total_checked_unplanned)
                        <span class="badge rounded-pill text-bg-info px-3 text-white">{{ $employee->total_checked_unplanned }}</span>
                    @endif
                </td>
                <td>
                    <div class="progress mt-1" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar" style="width: {{ $employee->total_progress }}%"></div>
                    </div>
                </td>
                <td class="text-end"> {{ $employee->total_progress }}</td>
            </x-selectable-row>
        @endforeach
    </tbody>
</x-selectable-table>
