<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanningHistory extends Model
{
    use HasFactory;
    protected $table = 'planning_histories';
    public $timestamps = false;

    protected $fillable = [
        'planning_assignment_id',
        'old_status',
        'new_status',
        'changed_by',
        'reason',
    ];
    public function logs()
{
    return $this->morphMany(ActivityLog::class, 'model');
}

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function planningAssignment()
    {
        return $this->belongsTo(PlanningAssignment::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
