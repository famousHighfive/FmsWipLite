<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentHistory extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'assignment_id', 'employee_id', 'old_manager_id', 'new_manager_id',
        'old_campaign_id', 'new_campaign_id', 'action_type', 'changed_by', 'reason'
    ];

    public function assignment() { return $this->belongsTo(Assignment::class); }

    public function employee() { return $this->belongsTo(Employee::class); }

    public function oldManager() { return $this->belongsTo(User::class, 'old_manager_id'); }

    public function newManager() { return $this->belongsTo(User::class, 'new_manager_id'); }

    public function oldCampaign() { return $this->belongsTo(Campaign::class, 'old_campaign_id'); }

    public function newCampaign() { return $this->belongsTo(Campaign::class, 'new_campaign_id'); }

    public function author() { return $this->belongsTo(User::class, 'changed_by'); }
    public function logs()
    {
        return $this->morphMany(ActivityLog::class, 'model');
    }
}
