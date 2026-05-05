<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimesheetHistory extends Model
{

    protected $table = 'timesheet_historys';

  

    protected $fillable = [
        'timesheet_id',
        'employee_id',
        'old_status',
        'new_status',
        'changed_by',
        'reason',
        'created_at',
    ];

}
