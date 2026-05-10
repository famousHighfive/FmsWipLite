<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentHistory;
use App\Models\Employee;
use App\Models\Campaign;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AssignmentController extends Controller
{
    /**
     * Affiche la liste des affectations selon le contexte
     */
    public function index(Request $request)
    {
        $view = $request->route()->getName();
        
        $query = Assignment::with(['employee', 'campaign', 'manager', 'position'])
            ->where('status', 'actif');

        // Selon la route, on peut filtrer
        if ($view === 'assignments.cp') {
            $query->whereHas('position', fn($q) => $q->where('code', 'CP'));
        } elseif ($view === 'assignments.sup') {
            $query->whereHas('position', fn($q) => $q->where('code', 'SUP'));
        } elseif ($view === 'assignments.tc') {
            $query->whereHas('position', fn($q) => $q->where('code', 'TC'));
        }

        $assignments = $query->latest()->get();

        return Inertia::render('Assignments/Index', [
            'assignments' => $assignments,
            'employees'   => Employee::where('status', 'actif')->get(),
            'campaigns'   => Campaign::where('status', 'active')->get(),
            'positions'   => Position::all(),
            'currentView' => $view
        ]);
    }

    /**
     * Affiche la vue hiérarchique des affectations
     */
    public function hierarchy()
    {
        // On récupère toutes les campagnes actives avec leurs affectations imbriquées
        // Campagne -> CP (manager_id null et position CP) -> SUP (manager_id CP) -> TC (manager_id SUP)
        $hierarchy = Campaign::where('status', 'active')
            ->with(['assignments' => function($query) {
                $query->where('status', 'actif')
                    ->with(['employee.position', 'position']);
            }])
            ->get()
            ->map(function($campaign) {
                // On structure les données pour le front-end
                $assignments = $campaign->assignments;
                
                // 1. Trouver les Chefs de Plateau (CP) de la campagne
                $cps = $assignments->filter(function($a) {
                    return $a->position->code === 'CP';
                })->map(function($cp) use ($assignments) {
                    // 2. Pour chaque CP, trouver ses Superviseurs (SUP)
                    $supervisors = $assignments->filter(function($a) use ($cp) {
                        return $a->position->code === 'SUP' && $a->manager_id === $cp->employee_id;
                    })->map(function($sup) use ($assignments) {
                        // 3. Pour chaque SUP, trouver ses Téléconseillers (TC)
                        $tcs = $assignments->filter(function($a) use ($sup) {
                            return $a->position->code === 'TC' && $a->manager_id === $sup->employee_id;
                        });
                        
                        $sup->teleconseillers = $tcs->values();
                        return $sup;
                    });
                    
                    $cp->supervisors = $supervisors->values();
                    return $cp;
                });

                return [
                    'id' => $campaign->id,
                    'name' => $campaign->name,
                    'status' => $campaign->status,
                    'cps' => $cps->values()
                ];
            });

        return Inertia::render('Assignments/Hierarchy', [
            'hierarchy' => $hierarchy
        ]);
    }

    /**
     * Enregistre une nouvelle affectation et crée une trace dans l'historique
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => [
                'required',
                'exists:employees,id',
                function ($attribute, $value, $fail) use ($request) {
                    // Règle : Un employé (hors CP) ne peut pas avoir deux affectations actives
                    $isCP = \App\Models\Position::where('id', $request->position_id)->where('code', 'CP')->exists();
                    $alreadyAssigned = \App\Models\Assignment::where('employee_id', $value)
                        ->where('status', 'actif')
                        ->exists();

                    if (!$isCP && $alreadyAssigned) {
                        $fail("Cet employé est déjà affecté à une autre campagne.");
                    }
                },
            ],
            'campaign_id' => 'required|exists:campaigns,id',
            'position_id' => 'required|exists:positions,id',
            'manager_id'  => 'nullable|exists:employees,id',
            'start_date'  => 'required|date',
        ]);

        // On utilise une transaction pour s'assurer que l'affectation et l'historique sont créés ensemble
        DB::transaction(function () use ($validated, $request) {
            // 1. Création de l'affectation dans la table 'assignments'
            $assignment = Assignment::create($validated);

            // 2. Création de la trace dans 'assignment_histories'
            AssignmentHistory::create([
                'assignment_id' => $assignment->id,
                'employee_id' => $validated['employee_id'],
                'new_manager_id' => $validated['manager_id'] ?? null,
                'new_campaign_id' => $validated['campaign_id'],
                'action_type' => 'assign',
                'changed_by' => Auth::id(),
                'reason' => $request->reason ?? 'Première affectation',
            ]);
        });

        return redirect()->back()->with('success', 'Affectation créée avec succès.');
    }

    /**
     * Gère la réaffectation (changement de manager) d'une ressource
     */
    public function reassign(Request $request, Assignment $assignment)
    {
        $validated = $request->validate([
            'manager_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'reason' => 'nullable|string',
        ]);

        DB::transaction(function () use ($assignment, $validated) {
            $oldManagerId = $assignment->manager_id;

            $assignment->update([
                'manager_id' => $validated['manager_id'],
                'start_date' => $validated['start_date'],
            ]);

            AssignmentHistory::create([
                'assignment_id' => $assignment->id,
                'employee_id' => $assignment->employee_id,
                'old_manager_id' => $oldManagerId,
                'new_manager_id' => $validated['manager_id'],
                'old_campaign_id' => $assignment->campaign_id,
                'new_campaign_id' => $assignment->campaign_id,
                'action_type' => 'transfer',
                'changed_by' => Auth::id(),
                'reason' => $validated['reason'] ?? 'Réaffectation manuelle',
            ]);
        });

        return redirect()->back()->with('success', 'Réaffectation effectuée.');
    }

    /**
     * Gère la libération ou le transfert d'une ressource (CP, SUP ou TC)
     */
    public function release(Request $request, Assignment $assignment)
    {
        $mode = $request->mode; 
        $newManagerId = $request->new_manager_id;

        DB::transaction(function () use ($assignment, $mode, $newManagerId, $request) {
            
            if ($mode === 'transfer' && $newManagerId) {
                Assignment::where('manager_id', $assignment->employee_id)
                    ->where('campaign_id', $assignment->campaign_id)
                    ->where('status', 'actif')
                    ->get()
                    ->each(function ($sub) use ($newManagerId) {
                        $oldManager = $sub->manager_id;
                        $sub->update(['manager_id' => $newManagerId]);

                        AssignmentHistory::create([
                            'assignment_id' => $sub->id,
                            'employee_id' => $sub->employee_id,
                            'old_manager_id' => $oldManager,
                            'new_manager_id' => $newManagerId,
                            'old_campaign_id' => $sub->campaign_id,
                            'new_campaign_id' => $sub->campaign_id,
                            'action_type' => 'transfer',
                            'changed_by' => Auth::id(),
                            'reason' => "Transfert automatique suite au départ de son manager",
                        ]);
                    });
            } 
            elseif ($mode === 'cascade') {
                $this->libererEnCascade($assignment->employee_id, $assignment->campaign_id);
            }

            $assignment->update([
                'status' => 'termine', 
                'end_date' => now()
            ]);

            AssignmentHistory::create([
                'assignment_id' => $assignment->id,
                'employee_id' => $assignment->employee_id,
                'old_manager_id' => $assignment->manager_id,
                'old_campaign_id' => $assignment->campaign_id,
                'action_type' => 'release',
                'changed_by' => Auth::id(),
                'reason' => $request->reason ?? "Libération effectuée (Mode: $mode)",
            ]);
        });

        return redirect()->back()->with('success', 'Ressource libérée avec succès.');
    }

    /**
     * Libération récursive pour le mode cascade
     */
    private function libererEnCascade($managerId, $campaignId)
    {
        $subordinates = Assignment::where('manager_id', $managerId)
            ->where('campaign_id', $campaignId)
            ->where('status', 'actif')
            ->get();

        foreach ($subordinates as $sub) {
            $sub->update(['status' => 'termine', 'end_date' => now()]);

            AssignmentHistory::create([
                'assignment_id' => $sub->id,
                'employee_id' => $sub->employee_id,
                'old_manager_id' => $sub->manager_id,
                'old_campaign_id' => $sub->campaign_id,
                'action_type' => 'release',
                'changed_by' => Auth::id(),
                'reason' => "Libération automatique (cascade) suite au départ de son manager",
            ]);

            $this->libererEnCascade($sub->employee_id, $campaignId);
        }
    }

    /**
     * Affiche l'historique des affectations
     */
    public function history()
    {
        $histories = AssignmentHistory::with(['employee', 'oldManager', 'newManager', 'oldCampaign', 'newCampaign', 'author'])
            ->latest()
            ->paginate(15);

        return Inertia::render('Assignments/History', [
            'histories' => $histories
        ]);
    }
}
