<i  {{ $attributes->merge([
    'class' => 'bi bi-info-circle text-muted cursor-pointer',
    'x-init' => 'new bootstrap.Tooltip($el)',
    'data-bs-toggle' => 'tooltip',
    'data-bs-placement' => 'bottom'
]) }} data-bs-title="{{ $title }}"></i>
