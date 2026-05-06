<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timesheet extends Model
{
    /** @use HasFactory<\Database\Factories\TimesheetsFactory> */
    use HasFactory;
    protected $fillable = [
        'employee_id',
        'period_start',
        'period_end',
        'status',
        'validated_by',
        'validated_at',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end'   => 'date',
        'validated_at' => 'datetime',
    ];
    public function entries()
    {
        return $this->hasMany(Timesheet_entries::class);
    }

      public function validator()
    {
        return $this->belongsTo(Employee::class, 'validated_by');
    }
}
