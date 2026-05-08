<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTimesheetRequest;
use App\Http\Requests\UpdateTimesheetRequest;
use App\Models\Assignment;
use App\Models\Employee;
use App\Models\Timesheet;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;

class TimesheetController extends Controller 
{
    /**
     * Affiche la liste des feuilles de temps (Calendrier).
     */
public function index()
{
    $user = auth()->user();
    $employee = $user->employee;
    $role = strtoupper($user->role->name); // Normalisation en majuscules

    // Préparation de la requête de base
    $query = Timesheet::with(['employee', 'validator', 'entries']);

    // --- LOGIQUE DE FILTRAGE PAR RÔLE ---

    if ($role === 'ADMIN') {
        // L'ADMIN voit TOUT le monde sans exception
        // On ne rajoute aucun "where", la requête reste brute
    } 
    elseif ($role === 'SUP' || $role === 'CP') {
        // Sécurité : Si SUP ou CP n'a pas de profil employé, il ne voit rien (évite le crash ID on null)
        if (!$employee) return Inertia::render('Timesheets/Calendar', ['calendar' => []]);

        // Filtrage par affectation directe
        $query->whereHas('employee.assignments', function ($q) use ($employee) {
            $q->where('manager_id', $employee->id)
              ->where('status', 'actif');
        });
    } 
    elseif ($role === 'TC') {
        if (!$employee) return Inertia::render('Timesheets/Calendar', ['calendar' => []]);
        // Le TC ne voit que lui-même
        $query->where('employee_id', $employee->id);
    }

    return Inertia::render('Timesheets/Calendar', [
        'calendar' => $query->latest()->get()
    ]);
}




    /**
     * Affiche le formulaire de création.
     */
    public function create()
    {
        return Inertia::render('Timesheets/Create');
    }

    /**
     * Enregistre une nouvelle ressource.
     */
 public function store(Request $request) 
{
    // 1. Créer l'employé
    $employee = Employee::create($request->all());

    // 2. L'assigner à un Manager (SUP/CP) via ta table assignments
    Assignment::create([
        'employee_id' => $employee.id,
        'manager_id' => auth()->user()->employee->id,
        'start_date' => now(),
        'status' => 'actif'
    ]);

    // 3. Créer sa feuille de route immédiatement pour qu'il apparaisse au calendrier
    Timesheet::create([
        'employee_id' => $employee->id,
        'period_start' => Carbon::now()->startOfWeek(),
        'period_end' => Carbon::now()->endOfWeek(),
        'status' => 'brouillon'
    ]);

    return redirect()->route('calendar.index');
}
    /**
     * Affiche une ressource spécifique.
     */
    public function show(Timesheet $timesheet)
    {
        return Inertia::render('Timesheets/Show', [
            'timesheet' => $timesheet->load(['employee', 'validator', 'entries'])
        ]);
    }

    /**
     * Affiche le formulaire d'édition.
     */
    public function edit(Timesheet $timesheet)
    {
        return Inertia::render('Timesheets/Edit', [
            'timesheet' => $timesheet
        ]);
    }

    /**
     * Met à jour la ressource.
     */
    public function update(UpdateTimesheetRequest $request, Timesheet $timesheet)
    {

    }

    /**
     * Supprime la ressource.
     */
    public function destroy(Timesheet $timesheet)
    {

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
