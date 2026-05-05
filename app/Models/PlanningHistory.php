<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanningHistory extends Model
{
    protected $table = 'planning_historys';


    protected $fillable = [
        'planning_assignment_id',
        'old_status',
        'new_status',
        'changed_by',
        'reason',
        'created_at',
    ];

}
