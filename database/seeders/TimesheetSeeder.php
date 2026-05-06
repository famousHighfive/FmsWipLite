<?php

// database/seeders/TimesheetSeeder.php
namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Timesheet;
use App\Models\TimesheetEntry;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TimesheetSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::all();
        $startDate = now()->startOfWeek()->subWeek(); // On seed la semaine dernière

        foreach ($employees as $employee) {
            // 1. Créer l'enveloppe
            $timesheet = Timesheet::create([
                'employee_id' => $employee->id,
                'period_start' => $startDate,
                'period_end' => (clone $startDate)->endOfWeek(),
                'status' => 'valide',
            ]);

            // 2. Créer les entrées pour chaque jour (Lundi à Vendredi)
            for ($i = 0; $i < 5; $i++) {
                $currentDate = (clone $startDate)->addDays($i);
                
                // Simulation : On récupère les heures prévues (via une méthode de ton modèle Employee par exemple)
                // Ici on met 7h par défaut pour l'exemple
                $planned = 7.00; 
                $real = 8.00;

                TimesheetEntry::create([
                    'timesheet_id' => $timesheet->id,
                    'date' => $currentDate,
                    'check_in' => '08:30:00',
                    'check_out' => '17:30:00',
                    'break_duration' => 60,
                    'planned_hours' => $planned,
                    'total_hours' => $real,
                    'overtime_hours' => $real - $planned
                ]);
            }
        }
    }
}