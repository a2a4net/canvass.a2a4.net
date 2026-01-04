<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyticsProgress extends Model
{
    protected $fillable = ['day', 'employee_id', 'planned', 'checked_planned', 'checked_unplanned', 'total_progress', 'median_time', 'work_time', 'dispersion', 'concentration'];

    protected $casts = [
        'day' => 'date',
    ];

    public $timestamps = false;

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
