<?php

namespace App\Models;

use App\Models\Employee;
use App\Models\PlanningModel;
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
        'validated_by',
        'validated_at',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    // Relation vers le modèle de planning
    public function planningModel()
    {
        return $this->belongsTo(PlanningModel::class, 'planning_model_id');
    }
    public function validator()
{
    return $this->belongsTo(Employee::class, 'validated_by');
}

}
