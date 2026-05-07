<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTimesheetRequest;
use App\Http\Requests\UpdateTimesheetRequest;
use App\Models\Timesheet;
use Inertia\Inertia;
use Illuminate\Http\Request;

class TimesheetController extends Controller 
{
    /**
     * Affiche la liste des feuilles de temps (Calendrier).
     */
    public function index()
    {
        $calendar = Timesheet::with(['employee', 'validator', 'entries'])
            ->latest()
            ->get();

        return Inertia::render('Timesheets/calendar', [
            'calendar' => $calendar
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
    public function store(StoreTimesheetRequest $request)
    {
        Timesheet::create($request->validated());
        return redirect()->route('timesheets.index');
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
        $timesheet->update($request->validated());
        return redirect()->route('timesheets.index');
    }

    /**
     * Supprime la ressource.
     */
    public function destroy(Timesheet $timesheet)
    {
        $timesheet->delete();
        return redirect()->route('timesheets.index');
    }
}
