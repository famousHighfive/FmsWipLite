<?php

// database/seeders/PlanningAssignmentSeeder.php
namespace Database\Seeders;

use App\Models\PlanningAssignment;
use App\Models\Employee;
use App\Models\PlanningModel;
use Illuminate\Database\Seeder;

class PlanningAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $model35h = PlanningModel::where('name', 'like', '%35h%')->first();
        $tcs = Employee::whereHas('position', fn($q) => $q->where('code', 'TC'))->get();
        $validator = Employee::whereHas('position', fn($q) => $q->where('code', 'CP'))->first();

        if (!$model35h || $tcs->isEmpty()) {
            return;
        }

        // Assigner le planning 35h à tous les TC
        foreach ($tcs as $tc) {
            PlanningAssignment::create([
                'planning_model_id' => $model35h->id,
                'employee_id'       => $tc->id,
                'start_date'        => now()->startOfMonth(),
                'end_date'          => now()->addMonths(6),
                'status'            => 'validé',
                'validated_by'      => $validator?->id,
                'validated_at'      => now(),
            ]);
        }
    }
}