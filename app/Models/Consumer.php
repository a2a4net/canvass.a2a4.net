<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Consumer extends Model
{
    protected $fillable = ['street', 'housenumber', 'apartment', 'lat', 'lon'];
    public $timestamps = false;

    public function getAddressAttribute(): string
    {
        return implode(', ', array_filter([
            $this->street,
            $this->housenumber,
            $this->apartment ? 'кв. ' . $this->apartment : null
        ]));
    }

    public function getCodeAttribute(): string
    {
        return 'VI' . str_pad($this->id, 8, '0', STR_PAD_LEFT);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
