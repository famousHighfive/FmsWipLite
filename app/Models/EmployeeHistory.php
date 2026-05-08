<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeHistory extends Model
{
    use HasFactory;

    protected $table = 'employee_histories';

    protected $fillable = [
        'employee_id',
        'old_position_id',
        'new_position_id',
        'old_status',
        'new_status',
        'changed_by',
        'reason',
    ];

    // -------------------------------------------------------
    // RELATIONS
    // -------------------------------------------------------

    /**
     * L'employé concerné par la modification
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * L'utilisateur qui a effectué la modification
     */
    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
    //
    public function logs()
{
    return $this->morphMany(ActivityLog::class, 'model');
}
}
