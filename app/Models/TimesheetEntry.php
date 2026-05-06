<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
        'comment',
    ];

    protected $casts = [
        'date'            => 'date',
        'check_in'        => 'datetime:H:i',
        'check_out'       => 'datetime:H:i',
        'total_hours'     => 'decimal:2',
        'planned_hours'   => 'decimal:2',
        'overtime_hours'  => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    // Une entrée appartient à une feuille
    public function timesheet()
    {
        return $this->belongsTo(Timesheet::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers utiles
    |--------------------------------------------------------------------------
    */

    public function isAbsent()
    {
        return !is_null($this->absence_type);
    }
}
