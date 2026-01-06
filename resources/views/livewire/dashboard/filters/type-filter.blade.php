<div wire:ignore class="d-flex">
    <div class="flex-fill d-flex flex-wrap gap-2 gap-xl-0 responsive-group" role="group">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary border-0 me-2">
            <i class="bi bi-house"></i>
        </a>

        @foreach([
            'progress' => ['label' => 'Виконання', 'icon' => 'bi-person-walking', 'tooltip' => 'Прогрес'],
            'time' => ['label' => 'Продуктивність', 'icon' => 'bi-speedometer2', 'tooltip' => 'Робочий час'],
            'density' => ['label' => 'Щільність', 'icon' => 'bi-bezier2', 'tooltip' => 'Концентрація точок'],
            'deviation' => ['label' => 'Відхилення', 'icon' => 'bi-exclamation-octagon', 'tooltip' => 'Відхилення від маршруту'],
        ] as $value => $label)
            <input type="radio" class="btn-check" name="type" id="btn-{{ $value }}" value="{{ $value }}" wire:model.live="activeType" autocomplete="off">

            <label class="btn btn-outline-primary px-2" for="btn-{{ $value }}" @if($label['tooltip']) data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="{{ __($label['tooltip']) }}"@endif>
                <i class="bi {{ $label['icon'] }}"></i> {{ __($label['label']) }}
            </label>
        @endforeach
    </div>

    <div class="ms-auto">
        <div class="btn-group" role="group">
            @foreach([
                'map' => ['icon' => 'bi-map', 'tooltip' => 'Мапа'],
                'list' => ['icon' => 'bi-list-ul', 'tooltip' => 'Список'],
            ] as $value => $label)
                <input type="radio" class="btn-check" name="view" id="atn-{{ $value }}" value="{{ $value }}" wire:model.live="activeView" autocomplete="off">
                <label class="btn btn-outline-secondary" for="atn-{{ $value }}" @if($label['tooltip']) data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="{{ __($label['tooltip']) }}"@endif><i class="bi {{ $label['icon'] }}"></i></label>
            @endforeach
        </div>
    </div>
</div>
