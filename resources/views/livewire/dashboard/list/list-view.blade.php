<div class="card">
    @if (!empty($employee->id))
        <div class="card-header">
            <span class="fw-bold">{{ $employee->name }}</span> <span class="float-end">{{ $filterDate }}</span>
        </div>
    @endif
    <div class="card-body pt-2 pb-0">
        @if ($points && $points->isNotEmpty())
            <table class="table table-sm table-hover">
                <thead>
                <tr>
                    <th style="width: 100px;">{{ __('Code') }}</th>
                    <th style="width: 100px;">{{ __('План') }}</th>
                    <th>{{ __('Адреса') }}</th>
                    <th style="width: 132px;">{{ __('Перевірено') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($points as $point)
                    <tr>
                        <td class="p-0">{{ $point->consumer->code }}</td>
                        <td class="p-0">{{ $point->day->toDateString() }}</td>
                        <td class="p-0">{{ $point->consumer->address }}</td>
                        <td class="p-0 text-end">
                            @if ($point->is_checked && $point->checked_at)
                                <span class="badge rounded-pill text-white bg-{{ $point->day->toDateString() == $point->checked_at->toDateString() ? 'success' : 'info' }}">
                                    {{ $point->checked_at->toDateTimeString() }}
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @elseif ($points)
            <div class="text-center p-4">{{ __('Нічого не знайдено') }}</div>
        @else
            <div class="text-center p-4">{{ __('Спочатку оберіть працівника') }}</div>
        @endif
    </div>

    @if ($points && $points->hasPages())
        <div class="card-footer pt-2">
            {{ $points->links('vendor.livewire.pagination-sm') }}
        </div>
    @endif
</div>
