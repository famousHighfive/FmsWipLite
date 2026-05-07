<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeHistory extends Model
{
    //
    public function logs()
{
    return $this->morphMany(ActivityLog::class, 'model');
}
}
