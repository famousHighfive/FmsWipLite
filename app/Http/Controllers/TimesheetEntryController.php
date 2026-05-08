<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTimesheetEntryRequest;
use App\Http\Requests\UpdateTimesheetEntryRequest;
use App\Models\Employee;
use App\Models\Timesheet;
use App\Models\TimesheetEntry;
use Carbon\Carbon;
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

    // On récupère soit les IDs de la sélection multiple, soit l'ID unique du Request
    // Note : Pour la sélection multiple, ton frontend doit envoyer 'timesheet_ids'
    $timesheetIds = $request->input('timesheet_ids', [$validated['timesheet_id']]);

    if (empty($timesheetIds)) {
        return back()->withErrors(['message' => 'Aucune feuille de temps sélectionnée.']);
    }

    foreach ($timesheetIds as $tsId) {
        $timesheet = Timesheet::findOrFail($tsId);

        // Sécurité : Ne pas modifier si déjà soumis
        if ($timesheet->status === 'soumis') continue;

        // Calcul totalHours
        $totalHours = 0;
        if ($validated['check_in'] && $validated['check_out']) {
            $start = Carbon::parse($validated['check_in']);
            $end = Carbon::parse($validated['check_out']);
            $totalHours = max(0, ($start->diffInMinutes($end) - ($validated['break_duration'] ?? 0)) / 60);
        }

        // CRUD
        TimesheetEntry::updateOrCreate(
            ['timesheet_id' => $tsId, 'date' => $validated['date']],
            [
                'check_in'       => $validated['check_in'],
                'check_out'      => $validated['check_out'],
                'break_duration' => $validated['break_duration'] ?? 0,
                'total_hours'    => $totalHours,
                'planned_hours'  => 7.0, // À dynamiser plus tard
                'overtime_hours' => $totalHours - 7.0,
                'comment'        => $validated['comment']
            ]
        );

        // Passage en valide si c'était en brouillon
        if ($timesheet->status === 'brouillon') {
            $timesheet->update(['status' => 'valide']);
        }
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
 public function destroy(TimesheetEntry $entry)
{
    $timesheet = $entry->timesheet;
    
    if ($timesheet->status !== 'soumis') {
        $entry->delete();
    }

    return back();
}

}
