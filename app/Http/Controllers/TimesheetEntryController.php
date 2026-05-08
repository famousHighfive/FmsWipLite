<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTimesheetEntryRequest;
use App\Http\Requests\UpdateTimesheetEntryRequest;
use App\Models\Employee;
use App\Models\TimesheetEntry;
use Inertia\Inertia;

class TimesheetEntryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $entries = TimesheetEntry::with(['timesheet'])->get();
        return Inertia::render('Timesheets/TimesCard', [
            'entries' => $entries
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
public function store(StoreTimesheetEntryRequest $request)
{
    $validated = $request->validated();
    $timesheet = \App\Models\Timesheet::findOrFail($validated['timesheet_id']);

    // --- RÈGLE DE STATUT ---
    // Si déjà soumis, personne ne peut modifier (même pas le SUP)
    if ($timesheet->status === 'soumis') {
        return back()->withErrors(['message' => 'Cette feuille est soumise, modification impossible.']);
    }

    // --- RÈGLE DE RÔLE (Exemple) ---
    // On vérifie que l'utilisateur connecté n'est pas un TC (car le TC ne peut pas ajouter/modifier)
    if (auth()->user()->role->name === 'tc') {
        abort(403, 'Action non autorisée pour votre profil.');
    }

    // ... calcul totalHours ...
    $start = \Carbon\Carbon::createFromFormat('H:i', $validated['check_in']);
    $end = \Carbon\Carbon::createFromFormat('H:i', $validated['check_out']);
    $totalHours = max(0, ($start->diffInMinutes($end) - ($validated['break_duration'] ?? 0)) / 60);

    $entry = TimesheetEntry::updateOrCreate(
        ['timesheet_id' => $validated['timesheet_id'], 'date' => $validated['date']],
        [
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
            'break_duration' => $validated['break_duration'] ?? 0,
            'total_hours' => $totalHours,
            'planned_hours' => 7.0, // À dynamiser via planning_models
            'overtime_hours' => $totalHours - 7.0,
            'comment' => $validated['comment']
        ]
    );

    // Si on enregistre, le statut passe à 'valide' (étape avant 'soumis')
    if ($timesheet->status === 'brouillon') {
        $timesheet->update(['status' => 'valide']);
    }

    return back();
}





    /**
     * Display the specified resource.
     */
 public function show($employeeId, $date)
{
    // On cherche l'entrée existante via la relation timesheet -> employee_id
    $entry = TimesheetEntry::whereHas('timesheet', function ($query) use ($employeeId) {
            $query->where('employee_id', $employeeId);
        })
        ->where('date', $date)
        ->first();

    return response()->json($entry);
}


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TimesheetEntry $timesheetEntry)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTimesheetEntryRequest $request, TimesheetEntry $timesheetEntry)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TimesheetEntry $timesheetEntry)
    {
        //
    }
}
