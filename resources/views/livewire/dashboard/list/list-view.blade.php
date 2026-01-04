<div class="card">
    <div class="card-body pt-1 pb-0" style="max-height: 850px;">
        @if ($points && $points->isNotEmpty())
            <table class="table table-sm table-hover">
                <thead>
                <tr>
                    <th>{{ __('ID') }}</th>
                    <th>{{ __('Планова дата') }}</th>
                    <th>{{ __('Адреса') }}</th>
                    <th class="text-end">{{ __('Перевірено') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($points as $point)
                    <tr>
                        <td class="p-0">{{ $point->consumer->id }}</td>
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
            <div class="text-center">{{ __('Нічого не знайдено') }}</div>
        @else
            <div class="text-center">{{ __('Спочатку оберіть працівника') }}</div>
        @endif
    </div>

    @if ($points && $points->hasPages())
        <div class="card-footer pt-2">
            {{ $points->links('vendor.livewire.pagination-sm') }}
        </div>
    @endif
</div>
