<?php

namespace App\Models;

use App\Models\Employee;
use App\Models\PlanningModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanningAssignment extends Model
{
    /** @use HasFactory<\Database\Factories\PlanningAssignmentsFactory> */
    use HasFactory;

    protected $fillable = [
        'planning_model_id',
        'employee_id',
        'start_date',
        'end_date',
        'status',
        'created_by',
        'validated_by',
        'validated_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'validated_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function planningModel()
    {
        return $this->belongsTo(PlanningModel::class, 'planning_model_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

 
    public function validator()
{
    return $this->belongsTo(Employee::class, 'validated_by');
}
public function logs()
{
    return $this->morphMany(ActivityLog::class, 'model');
}
}
