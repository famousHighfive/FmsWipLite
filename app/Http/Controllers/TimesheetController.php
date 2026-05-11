<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTimesheetRequest;
use App\Models\Assignment;
use App\Models\Employee;
use App\Models\Timesheet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TimesheetController extends Controller
{
    /**
     * AFFICHAGE DU CALENDRIER (INDEX)
     * Récupère et filtre les feuilles de temps selon les droits d'accès du rôle utilisateur.
     */
    public function index()
    {
        $user = auth()->user();
        $employee = $user->employee;
        $role = strtoupper($user->role->name); // Normalisation pour faciliter les tests

        // Chargement des relations nécessaires pour éviter le problème "N+1 queries"
        $query = Timesheet::with(['employee', 'validator', 'entries']);

        // --- FILTRAGE DE SÉCURITÉ ---
        if ($role === 'ADMIN') {
            // L'ADMIN voit l'intégralité des données sans restriction
        } 
        elseif ($role === 'SUP' || $role === 'CP') {
            // Sécurité : Si le manager n'a pas de profil employé, on retourne une liste vide
            if (!$employee) {
                return Inertia::render('Timesheets/Calendar', ['calendar' => []]);
            }

            // Filtre : Un manager ne voit que les employés qui lui sont assignés activement
            $query->whereHas('employee.assignments', function ($q) use ($employee) {
                $q->where('manager_id', $employee->id)
                  ->where('status', 'actif');
            });
        } 
        elseif ($role === 'TC') {
            if (!$employee) {
                return Inertia::render('Timesheets/Calendar', ['calendar' => []]);
            }
            // Filtre : Le Téléconseiller ne voit que ses propres pointages
            $query->where('employee_id', $employee->id);
        }

        return Inertia::render('Timesheets/Calendar', [
            'calendar' => $query->latest()->get(),
        ]);
    }

    /**
     * CRÉATION D'UN NOUVEL EMPLOYÉ ET INITIALISATION
     * Crée le profil, l'assignation hiérarchique et la première feuille de temps.
     */
    public function store(Request $request)
    {
        // 1. Création de la fiche employé
        $employee = Employee::create($request->all());

        // 2. Assignation automatique au manager connecté (SUP/CP)
        Assignment::create([
            'employee_id' => $employee->id,
            'manager_id'  => auth()->user()->employee->id,
            'start_date'  => now(),
            'status'      => 'actif',
        ]);

        // 3. Initialisation immédiate de la feuille de temps pour la semaine en cours
        Timesheet::create([
            'employee_id'  => $employee->id,
            'period_start' => Carbon::now()->startOfWeek(),
            'period_end'   => Carbon::now()->endOfWeek(),
            'status'       => 'brouillon',
        ]);

        return redirect()->route('calendar.index');
    }

    /**
     * CONSULTATION DÉTAILLÉE
     * Affiche les détails complets d'une semaine de pointage spécifique.
     */
    public function show(Timesheet $timesheet)
    {
        return Inertia::render('Timesheets/Show', [
            'timesheet' => $timesheet->load(['employee', 'validator', 'entries']),
        ]);
    }

    /**
     * VALIDATION ET VERROUILLAGE (SUBMIT)
     * Marque la feuille comme terminée, ce qui bloque toute modification ultérieure.
     */
    public function submit(Timesheet $timesheet)
    {
        $user = auth()->user();
        $role = strtoupper($user->role->name);
        $employee = $user->employee;

        // Préparation des données de validation
        $updateData = [
            'status'       => 'soumis',
            'validated_at' => now(),
        ];

        // --- ATTRIBUTION DU VALIDEUR ---
        if ($role === 'ADMIN') {
            // L'administrateur peut valider sans être lui-même un employé
            $updateData['validated_by'] = $employee ? $employee->id : null;
        } 
        else {
            // Un Chef de Plateau (CP) doit obligatoirement avoir un profil employé pour valider
            if (!$employee) {
                return back()->withErrors(['message' => 'Profil employé manquant pour valider.']);
            }
            $updateData['validated_by'] = $employee->id;
        }

        // Exécution du verrouillage
        $timesheet->update($updateData);

        return back(); // Retourne sur la page actuelle avec les données fraîches
    }

   
    public function create() { return Inertia::render('Timesheets/Create'); }
    public function edit(Timesheet $timesheet) { }
    public function update(UpdateTimesheetRequest $request, Timesheet $timesheet) { }
    public function destroy(Timesheet $timesheet) {  }
}
