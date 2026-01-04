<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = ['day', 'employee_id', 'consumer_id', 'is_checked', 'checked_at'];

    public $timestamps = false;

    protected $casts = [
        'day' => 'date',
        'checked_at' => 'datetime',
    ];

    public function checkIn(?Carbon $timestamp = null): void
    {
        $timestamp ??= Carbon::now();

        $this->update([
            'is_checked' => true,
            'checked_at' => $timestamp->toDateTime(),
        ]);
    }

    public function consumer(): BelongsTo
    {
        return $this->belongsTo(Consumer::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function scopeIsChecked(Builder $query): Builder
    {
        return $query->where('is_checked', true);
    }

    public function scopeIsNotChecked(Builder $query): Builder
    {
        return $query->where('is_checked', false);
    }

    public function scopeSearch(Builder $query, ?string $value): Builder
    {
        return $query->when($value, function ($q, $search) {
            $q->where('name', 'like', '%' . str_replace(['%', '_'], ['\%', '\_'], $search) . '%');
        });
    }
}
