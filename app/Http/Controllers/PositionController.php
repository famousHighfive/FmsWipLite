<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PositionController extends Controller
{
    /**
     * Liste des postes
     */
    public function index()
    {
        $positions = Position::withCount('employees')->get();

        return Inertia::render('Positions/Index', [
            'positions' => $positions,
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Fiche détaillée d'un poste avec ses employés
     */
    public function show(Position $position)
    {
        $position->load('employees');

        return Inertia::render('Positions/Show', [
            'position' => $position,
        ]);
    }
    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Position $position)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Position $position)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Position $position)
    {
        //
    }
}
