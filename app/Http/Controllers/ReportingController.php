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

use App\Exports\EmployeesExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

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
                'campaigns' => Campaign::count(),
                'assignments' => Assignment::where('status', 'actif')->count(),
                'workedHours' => TimesheetEntry::sum('total_hours'),
                'overtimeHours' => TimesheetEntry::sum('overtime_hours'),
                'pendingTimesheets' => Timesheet::where('status', 'soumis')->count(),
            ],

            'campaignStats' => Campaign::withCount(['assignments' => function($query) {
                $query->where('assignments.status', 'actif');
            }])
            ->latest()
            ->take(5)
            ->get(),

            'employeeStats' => Employee::withSum('user.timesheets.entries', 'total_hours')
                ->withSum('user.timesheets.entries', 'planned_hours')
                ->latest()
                ->take(5)
                ->get(),

            'planningGaps' => [
                'planned' => (float) TimesheetEntry::sum('planned_hours') ?: 1, // Avoid division by zero
                'worked' => (float) TimesheetEntry::sum('total_hours'),
                'gap' => TimesheetEntry::select(
                    DB::raw('SUM(total_hours - planned_hours) as total_gap')
                )->value('total_gap') ?: 0,
            ],

            'monthlyEvolution' => TimesheetEntry::select(
                DB::raw('DATE_FORMAT(date, "%Y-%m") as month'),
                DB::raw('SUM(total_hours) as total_worked'),
                DB::raw('SUM(planned_hours) as total_planned')
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->take(6)
            ->get()
        ]);
    }

    /**
     * Export des données en Excel
     */
    public function exportExcel()
    {
        return Excel::download(new EmployeesExport, 'rapport_employes_' . now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * Export des données en PDF
     */
    public function exportPdf()
    {
        $data = [
            'stats' => [
                'employees' => Employee::count(),
                'active' => Employee::where('status', 'actif')->count(),
                'campaigns' => Campaign::count(),
                'workedHours' => TimesheetEntry::sum('total_hours'),
            ],
            'campaigns' => Campaign::withCount('assignments')->get(),
            'date' => now()->format('d/m/Y H:i'),
        ];

        $pdf = Pdf::loadView('reports.dashboard_pdf', $data);
        return $pdf->download('rapport_decisionnel_' . now()->format('Y-m-d') . '.pdf');
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

    // public function exportPdf()
    // {
    //     //
    // }

    // public function exportExcel()
    // {
    //     //
    // }
}
