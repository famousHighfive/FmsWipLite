<?php

// database/seeders/LogHistorySeeder.php
namespace Database\Seeders;

use App\Models\TimesheetHistory;
use App\Models\AssignmentHistory;
use App\Models\ActivityLog;
use App\Models\Timesheet;
use App\Models\Assignment;
use App\Models\User;
use Illuminate\Database\Seeder;

class LogHistorySeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $timesheet = Timesheet::first();
        $assignment = Assignment::first();

        if (!$timesheet || !$assignment) return;

        // Log d'activité simple
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'update',
            'model_type' => get_class($timesheet),
            'model_id' => $timesheet->id,
            'description' => 'Validation de la feuille de temps de la semaine 18',
            'ip_address' => '127.0.0.1'
        ]);

        // Historique de statut
        TimesheetHistory::create([
            'timesheet_id' => $timesheet->id,
            'employee_id' => $timesheet->employee_id,
            'old_status' => 'brouillon',
            'new_status' => 'valide',
            'changed_by' => $user->id,
            'reason' => 'Vérification effectuée par le superviseur'
        ]);

        // Historique d'affectation
        $campaigns = \App\Models\Campaign::limit(2)->get();
        AssignmentHistory::create([
            'assignment_id' => $assignment->id,
            'employee_id' => $assignment->employee_id,
            'action_type' => 'transfer',
            'old_campaign_id' => $campaigns[0]->id ?? null,
            'new_campaign_id' => $campaigns[1]->id ?? null,
            'changed_by' => $user->id,
            'reason' => 'Besoin de renfort sur la campagne Orange'
        ]);
    }
}