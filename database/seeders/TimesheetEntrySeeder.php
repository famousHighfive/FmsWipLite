<?php

// database/seeders/TimesheetEntrySeeder.php
namespace Database\Seeders;

use App\Models\Timesheet;
use App\Models\TimesheetEntry;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TimesheetEntrySeeder extends Seeder
{
    public function run(): void
    {
        $timesheets = Timesheet::all();

        foreach ($timesheets as $timesheet) {
            $start = Carbon::parse($timesheet->period_start);
            
            // On génère 5 jours de travail (Lundi-Vendredi)
            for ($i = 0; $i < 5; $i++) {
                $date = (clone $start)->addDays($i);
                $planned = 7.00; // Heures prévues (à dynamiser plus tard via planning_models)
                $total = 8.00;   // Heures réelles effectuées

                TimesheetEntry::create([
                    'timesheet_id'   => $timesheet->id,
                    'date'           => $date->format('Y-m-d'),
                    'check_in'       => '08:00:00',
                    'check_out'      => '17:00:00',
                    'break_duration' => 60, // 60 minutes
                    'planned_hours'  => $planned,
                    'total_hours'    => $total,
                    'overtime_hours' => $total - $planned,
                    'comment'        => $i === 4 ? 'Fin de semaine chargée' : null,
                ]);
            }
        }
    }
}