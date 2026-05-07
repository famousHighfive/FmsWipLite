<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimesheetHistory extends Model
{
    use HasFactory;
    // Pas de timestamps standards car created_at est suffisant
    public $timestamps = false;

    protected $fillable = [
        'timesheet_id', 'employee_id', 'old_status', 
        'new_status', 'changed_by', 'reason'
    ];

    public function timesheet() { return $this->belongsTo(Timesheet::class); }

    public function employee() { return $this->belongsTo(Employee::class); }

    public function author() { return $this->belongsTo(User::class, 'changed_by'); }
}
