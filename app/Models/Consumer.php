<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Consumer extends Model
{
    protected $fillable = ['street', 'housenumber', 'lat', 'lon'];
    public $timestamps = false;

    public function getAddressAttribute(): string
    {
        return trim($this->street . ', ' . $this->housenumber);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
