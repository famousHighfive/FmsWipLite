<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTimesheetRequest;
use App\Http\Requests\UpdateTimesheetRequest;
use App\Models\Timesheet;
use Inertia\Inertia;

class TimesheetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
  //
        $calendar = Timesheet::with(['employee' , 'validator', 'entries'])->latest()->get();
        return Inertia::render('Timesheets/calendar', [
            'calendar' => $calendar
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(){

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTimesheetRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Timesheet $timesheet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Timesheet $timesheet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTimesheetRequest $request, Timesheet $timesheet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Timesheet $timesheet)
    {
        //
    }
}
