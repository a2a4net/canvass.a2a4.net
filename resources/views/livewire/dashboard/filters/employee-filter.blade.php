<div class="input-group">
    <input type="text" class="form-control px-3 rounded-start-4" value="" placeholder="{{ __('Контролер...') }}" wire:model.live.debounce.300ms="search">

    <button class="btn btn-outline-secondary border-start-0 border-secondary-subtle rounded-end-4" type="button" @if($search) wire:click="$set('search', '')" @else disabled="disabled" @endif>
        <i class="bi bi-x-lg"></i>
    </button>
</div>
