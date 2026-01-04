<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_active'];

    protected $casts = [
        'is_active' => 'bool',
    ];

    public function scopeSearch(Builder $query, ?string $value): Builder
    {
        return $query->when($value, function ($q, $search) {
            $q->where('name', 'like', '%' . str_replace(['%', '_'], ['\%', '\_'], $search) . '%');
        });
    }

    public function scopeIsActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function analyticsProgress(): HasMany
    {
        return $this->hasMany(AnalyticsProgress::class);
    }
}
