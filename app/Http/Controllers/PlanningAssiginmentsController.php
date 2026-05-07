<?php

namespace App\Http\Controllers;

use App\Models\planning_assiginments;
use App\Http\Requests\Storeplanning_assiginmentsRequest;
use App\Http\Requests\Updateplanning_assiginmentsRequest;
use App\Models\PlanningAssignment;
use GuzzleHttp\Psr7\Request;

class PlanningAssiginmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(PlanningAssignment $planning_assiginments)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(planning_assiginments $planning_assiginments)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Updateplanning_assiginmentsRequest $request, planning_assiginments $planning_assiginments)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(planning_assiginments $planning_assiginments)
    {
        //
    }
}
