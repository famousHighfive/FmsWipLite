<?php

namespace App\Http\Controllers;

use App\Models\Timesheet;
use App\Models\TimesheetEntry;
use App\Models\Employee;
use App\Models\Assignment;
use App\Models\PlanningAssignment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TimesheetController extends Controller 
{
    public function index()
    {
        $user = Auth::user();
        $employee = $user->employee;

        // Si l'utilisateur n'est pas lié à un employé, on retourne une liste vide ou gère le cas admin
        if (!$employee && !$user->hasRole('admin')) {
            return Inertia::render('Timesheets/Index', ['timesheets' => []]);
        }

        $query = Timesheet::with(['employee.user', 'validator.user']);

        if ($user->hasRole('tc')) {
            $query->where('employee_id', $employee->id);
        } elseif ($user->hasRole('sup')) {
            $tcIds = Assignment::where('manager_id', $employee->id)->pluck('employee_id');
            $query->whereIn('employee_id', $tcIds->push($employee->id));
        } elseif ($user->hasRole('cp')) {
            // Le CP voit les feuilles de temps de ses SUP
            $supIds = Assignment::where('manager_id', $employee->id)->pluck('employee_id');
            $query->whereIn('employee_id', $supIds);
        }

        return Inertia::render('Timesheets/Index', [
            'timesheets' => $query->latest()->get()
        ]);
    }

    public function show($id)
    {
        $timesheet = Timesheet::with(['employee.user', 'entries', 'validator.user'])->findOrFail($id);

        return Inertia::render('Timesheets/Show', [
            'timesheet' => $timesheet
        ]);
    }

    public function entry(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee && !$user->hasRole('admin')) {
            return redirect()->route('dashboard')->with('error', 'Aucun profil employé associé à votre compte.');
        }
        
        // Par défaut, semaine en cours
        $date = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::now();
        $startOfWeek = $date->copy()->startOfWeek();
        $endOfWeek = $date->copy()->endOfWeek();

        // Récupérer les subordonnés
        $subordinates = collect();
        if ($employee) {
            $subordinates = Assignment::where('manager_id', $employee->id)
                ->where('status', 'actif')
                ->with('employee.user')
                ->get()
                ->map(fn($a) => $a->employee)
                ->filter(); // Éviter les nuls
        }

        // Si pas de subordonnés (ex: TC ou Admin ou SUP sans équipe), on permet la saisie pour soi-même si employé
        if ($subordinates->isEmpty() && $employee) {
             $subordinates = collect([$employee]);
        }

        // Récupérer les plannings pour cette période
        $plannings = PlanningAssignment::whereIn('employee_id', $subordinates->pluck('id'))
            ->where('status', 'validé')
            ->where(function($q) use ($startOfWeek, $endOfWeek) {
                $q->whereBetween('start_date', [$startOfWeek, $endOfWeek])
                  ->orWhereBetween('end_date', [$startOfWeek, $endOfWeek]);
            })
            ->with('planningModel')
            ->get();

        return Inertia::render('Timesheets/Entry', [
            'subordinates' => $subordinates->values(),
            'plannings' => $plannings,
            'startDate' => $startOfWeek->format('Y-m-d'),
            'endDate' => $endOfWeek->format('Y-m-d'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'period_start' => 'required|date',
            'period_end' => 'required|date',
            'entries' => 'required|array',
            'entries.*.date' => 'required|date',
            'entries.*.check_in' => 'nullable|string',
            'entries.*.check_out' => 'nullable|string',
            'entries.*.break_duration' => 'nullable|integer',
            'entries.*.absence_type' => 'nullable|string',
        ]);

        $timesheet = Timesheet::updateOrCreate(
            [
                'employee_id' => $validated['employee_id'],
                'period_start' => $validated['period_start'],
                'period_end' => $validated['period_end'],
            ],
            ['status' => 'soumis']
        );

        foreach ($validated['entries'] as $entryData) {
            $entry = $timesheet->entries()->updateOrCreate(
                ['date' => $entryData['date']],
                $entryData
            );
            $entry->calculateHours();
            $entry->save();
        }

        return redirect()->back()->with('success', 'Heures enregistrées et soumises.');
    }

    public function validation()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee && !$user->hasRole('admin')) {
            return redirect()->route('dashboard')->with('error', 'Accès refusé.');
        }

        $query = Timesheet::with(['employee.user', 'entries'])
            ->where('status', 'soumis');

        if ($user->hasRole('cp') && $employee) {
            $supIds = Assignment::where('manager_id', $employee->id)->pluck('employee_id');
            $query->whereIn('employee_id', $supIds);
        } elseif ($user->hasRole('sup') && $employee) {
            // Un superviseur pourrait valider les TC ? (Selon le CDC : CP valide SUP, SUP saisit TC)
            $tcIds = Assignment::where('manager_id', $employee->id)->pluck('employee_id');
            $query->whereIn('employee_id', $tcIds);
        }

        return Inertia::render('Timesheets/Validation', [
            'pendingTimesheets' => $query->latest()->get()
        ]);
    }

    public function approve(Timesheet $timesheet)
    {
        $user = Auth::user();
        if (!$user->employee) {
            return redirect()->back()->with('error', 'Action impossible : profil employé manquant.');
        }

        $timesheet->update([
            'status' => 'valide',
            'validated_by' => $user->employee->id,
            'validated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Feuille de temps validée.');
    }

    public function reject(Timesheet $timesheet)
    {
        $timesheet->update(['status' => 'brouillon']);

        return redirect()->back()->with('warning', 'Feuille de temps renvoyée en brouillon.');
    }

    public function history()
    {
        return $this->index();
    }

    public function gaps()
    {
        return Inertia::render('Timesheets/Gaps');
    }

    public function overtime()
    {
        return Inertia::render('Timesheets/Overtime');
    }

    /**
 * Soumet définitivement la feuille de temps (Verrouillage).
 */
/**
 * Validation finale par le Chef de Plateau (CP)
 */
public function submit(Timesheet $timesheet)
{
    // 1. Vérification de sécurité : Seul un CP (ou Admin) devrait pouvoir faire ça
    // if (auth()->user()->role->code !== 'CP') { abort(403); }

    // 2. Mise à jour de la feuille de temps
    $timesheet->update([
        'status' => 'soumis',
        'validated_by' => auth()->user()->employee->id, // L'ID de l'employé qui valide
        'validated_at' => now(), // Horodatage précis
    ]);

    // 3. Optionnel : On peut aussi verrouiller les entrées individuelles
    // $timesheet->entries()->update(['status' => 'soumis']);

    return back()->with('success', 'La feuille de temps a été validée par ' . auth()->user()->name);
}


}
