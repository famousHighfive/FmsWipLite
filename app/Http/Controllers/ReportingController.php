<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Employee;
use App\Models\Assignment;
use App\Models\Timesheet;
use App\Models\TimesheetEntry;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReportingController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DASHBOARD ADMIN
    |--------------------------------------------------------------------------
    */

    public function admin()
    {
        return Inertia::render('Dashboard/Admin', [

            'stats' => [

                'employees' => Employee::count(),

                'activeEmployees' => Employee::where('status', 'actif')->count(),

                'campaigns' => Campaign::where('status', 'active')->count(),

                'assignments' => Assignment::where('status', 'active')->count(),

                'workedHours' => TimesheetEntry::sum('total_hours'),

                'overtimeHours' => TimesheetEntry::sum('overtime_hours'),

                'pendingTimesheets' => Timesheet::where('status', 'submitted')->count(),

            ],

            'campaignStats' => Campaign::withCount('assignments')
                ->latest()
                ->take(5)
                ->get(),

            'planningGaps' => [

                'planned' => TimesheetEntry::sum('planned_hours'),

                'worked' => TimesheetEntry::sum('total_hours'),

                'gap' => TimesheetEntry::select(
                    DB::raw('SUM(total_hours - planned_hours) as total_gap')
                )->value('total_gap'),
            ]
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | STATISTIQUES GÉNÉRALES
    |--------------------------------------------------------------------------
    */

    public function generalStats()
    {
        return Inertia::render('Dashboard/Statistiques', [

            'campaigns' => Campaign::withCount('assignments')->get(),

            'employees' => Employee::latest()->take(10)->get(),

            'workedHours' => TimesheetEntry::sum('total_hours'),

            'plannedHours' => TimesheetEntry::sum('planned_hours'),

            'overtimeHours' => TimesheetEntry::sum('overtime_hours'),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ALERTES & NOTIFICATIONS
    |--------------------------------------------------------------------------
    */

    public function alerts()
    {
        return Inertia::render('Dashboard/Alerts', [

            'logs' => ActivityLog::latest()
                ->take(20)
                ->get()
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD CHEF DE PLATEAU
    |--------------------------------------------------------------------------
    */

    public function cp()
    {
        return Inertia::render('Dashboard/ChefPlateau', [

            'stats' => [

                'supervisors' => Assignment::whereHas('position', function ($q) {
                    $q->where('code', 'SUP');
                })->count(),

                'teleconseillers' => Assignment::whereHas('position', function ($q) {
                    $q->where('code', 'TC');
                })->count(),

                'workedHours' => TimesheetEntry::sum('total_hours'),

                'overtimeHours' => TimesheetEntry::sum('overtime_hours'),

                'pendingTimesheets' => Timesheet::where('status', 'submitted')->count(),
            ]
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD SUPERVISEUR
    |--------------------------------------------------------------------------
    */

    public function sup()
    {
        return Inertia::render('Dashboard/Superviseur', [

            'stats' => [

                'teamHours' => TimesheetEntry::sum('total_hours'),

                'overtimeHours' => TimesheetEntry::sum('overtime_hours'),

                'pendingTimesheets' => Timesheet::where('status', 'submitted')->count(),
            ]
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD TELECONSEILLER
    |--------------------------------------------------------------------------
    */

    public function tc()
    {
        return Inertia::render('Dashboard/TeleConseiller', [

            'stats' => [

                'workedHours' => TimesheetEntry::sum('total_hours'),

                'plannedHours' => TimesheetEntry::sum('planned_hours'),

                'overtimeHours' => TimesheetEntry::sum('overtime_hours'),
            ]
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | REPORTS
    |--------------------------------------------------------------------------
    */

    public function hr()
    {
        return Inertia::render('Reports/Hr');
    }

    public function campaigns()
    {
        return Inertia::render('Reports/Campaigns');
    }

    public function assignments()
    {
        return Inertia::render('Reports/Assignments');
    }

    public function timesheets()
    {
        return Inertia::render('Reports/Timesheets');
    }

    /*
    |--------------------------------------------------------------------------
    | ANALYTICS & KPI
    |--------------------------------------------------------------------------
    */

    public function analytics()
    {
        return Inertia::render('Reports/Analytics');
    }

    public function kpis()
    {
        return Inertia::render('Reports/Kpis');
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORTS
    |--------------------------------------------------------------------------
    */

    public function exportPdf()
    {
        //
    }

    public function exportExcel()
    {
        //
    }
}
