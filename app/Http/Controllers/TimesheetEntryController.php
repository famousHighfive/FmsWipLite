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
    
    // ... tes calculs d'heures existants ...

    // 1. Mise à jour ou création de l'entrée
    $entry = TimesheetEntry::updateOrCreate(
        ['timesheet_id' => $validated['timesheet_id'], 'date' => $validated['date']],
        [
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
            'break_duration' => $validated['break_duration'] ?? 0,
            'total_hours' => $totalHours,
            'planned_hours' => 7.0, 
            'overtime_hours' => $totalHours - 7.0,
            'comment' => $validated['comment']
        ]
    );

    // 2. On passe la Timesheet parente en 'valide' dès qu'une modification est faite
    $entry->timesheet()->update(['status' => 'valide']);

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
