<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanningModel extends Model
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

    public function assignments()
    {
        return $this->hasMany(PlanningAssignment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
