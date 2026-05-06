<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TimesheetEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'timesheet_id',
        'date',
        'check_in',
        'check_out',
        'break_duration',
        'total_hours',
        'planned_hours',
        'overtime_hours',
        'absence_type',
        'comment'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function timesheet(): BelongsTo
    {
        return $this->belongsTo(Timesheet::class);
    }
public function calculateHours()
{
    if ($this->check_in && $this->check_out) {
        $start = Carbon::parse($this->check_in);
        $end = Carbon::parse($this->check_out);

        // Différence en heures moins la pause convertie en heures
        $diff = $start->diffInMinutes($end) / 60;
        $this->total_hours = $diff - ($this->break_duration / 60);

        // Calcul des heures sup
        $this->overtime_hours = max(0, $this->total_hours - $this->planned_hours);
    }
}
}
