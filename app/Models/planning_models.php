<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class planning_models extends Model
{
    /** @use HasFactory<\Database\Factories\PlanningModelsFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'monday_hours',
        'tuesday_hours',
        'wednesday_hours',
        'thursday_hours',
        'friday_hours',
        'saturday_hours',
        'sunday_hours',
        'total_hours',
        'created_by',
    ];

    // Relation inverse : un employé a plusieurs affectations
    public function assignments()
    {
        return $this->hasMany(PlanningAssignment::class, 'employee_id');
    }

}
