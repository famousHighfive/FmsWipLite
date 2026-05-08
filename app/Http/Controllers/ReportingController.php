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

                'pendingTimesheets' => Timesheet::where('status', 'soumis')->count(),

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

                'pendingTimesheets' => Timesheet::where('status', 'soumis')->count(),
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

                'pendingTimesheets' => Timesheet::where('status', 'soumis')->count(),
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
        return Inertia::render('Reports/Hr', [
            'stats' => [
                'total' => Employee::count(),
                'active' => Employee::where('status', 'actif')->count(),
                'inactive' => Employee::where('status', 'inactif')->count(),
                'suspended' => Employee::where('status', 'suspendu')->count(),
            ],
            'positionDistribution' => DB::table('employees')
                ->join('positions', 'employees.position_id', '=', 'positions.id')
                ->select('positions.name', DB::raw('count(*) as count'))
                ->groupBy('positions.name')
                ->get()
        ]);
    }

    public function campaigns()
    {
        return Inertia::render('Reports/Campaigns', [
            'campaigns' => Campaign::withCount(['assignments' => function($query) {
                $query->where('status', 'actif');
            }])->get(),
            'stats' => [
                'total' => Campaign::count(),
                'active' => Campaign::where('status', 'active')->count(),
                'closed' => Campaign::where('status', 'closed')->count(),
            ]
        ]);
    }

    public function assignments()
    {
        return Inertia::render('Reports/Assignments', [
            'assignments' => Assignment::with(['employee.user', 'campaign', 'manager.user', 'position'])
                ->latest()
                ->get()
        ]);
    }

    public function timesheets()
    {
        return Inertia::render('Reports/Timesheets', [
            'stats' => [
                'totalHours' => TimesheetEntry::sum('total_hours'),
                'totalPlanned' => TimesheetEntry::sum('planned_hours'),
                'totalOvertime' => TimesheetEntry::sum('overtime_hours'),
            ],
            'weeklyStats' => TimesheetEntry::select(
                DB::raw('YEARWEEK(date) as week'),
                DB::raw('SUM(total_hours) as hours'),
                DB::raw('SUM(planned_hours) as planned')
            )
            ->groupBy('week')
            ->orderBy('week', 'desc')
            ->take(8)
            ->get()
        ]);
    }

    public function team()
    {
        $user = auth()->user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->back()->with('error', 'Profil employé manquant.');
        }

        $teamIds = Assignment::where('manager_id', $employee->id)
            ->where('status', 'actif')
            ->pluck('employee_id');

        return Inertia::render('Reports/Team', [
            'team' => Employee::whereIn('id', $teamIds)->with('user', 'position')->get(),
            'stats' => [
                'totalMembers' => count($teamIds),
                'totalHours' => TimesheetEntry::whereIn('employee_id', $teamIds)->sum('total_hours'),
            ]
        ]);
    }

    public function productivity()
    {
        $stats = DB::table('timesheet_entries')
            ->join('employees', 'timesheet_entries.employee_id', '=', 'employees.id')
            ->join('users', 'employees.user_id', '=', 'users.id')
            ->select(
                'users.name',
                DB::raw('SUM(planned_hours) as planned'),
                DB::raw('SUM(total_hours) as worked'),
                DB::raw('SUM(total_hours) / SUM(planned_hours) * 100 as ratio')
            )
            ->where('planned_hours', '>', 0)
            ->groupBy('users.name', 'employees.id')
            ->get();

        return Inertia::render('Reports/Productivity', [
            'productivityStats' => $stats
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ANALYTICS & KPI
    |--------------------------------------------------------------------------
    */

    public function analytics()
    {
        return $this->timesheets(); // Rediriger vers timesheets pour l'instant
    }

    public function kpis()
    {
        return $this->hr(); // Rediriger vers HR pour l'instant
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORTS
    |--------------------------------------------------------------------------
    */

    public function exportExcel()
    {
        $employees = Employee::all();
        $csvExporter = new \App\Services\CsvExporter();
        return $csvExporter->export($employees, 'export_employes.csv');
    }
}
