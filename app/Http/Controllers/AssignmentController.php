<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentHistory;
use App\Models\Employee;
use App\Models\Campaign;
use App\Models\Position;
use App\Enums\AssignmentStatus;
use App\Enums\CampaignStatus;
use App\Enums\PositionCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AssignmentController extends Controller
{
    /**
     * Liste globale des historiques d'affectation
     */
    public function history()
    {
        $history = AssignmentHistory::with(['employee', 'author', 'newCampaign', 'oldCampaign'])
            ->latest()
            ->paginate(20);

        return Inertia::render('Affectations/History', [
            'history' => $history
        ]);
    }

    /**
     * Affecter un CP
     */
    public function storeCP(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'campaign_id' => 'required|exists:campaigns,id',
            'start_date' => 'required|date',
            'reason' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $campaign = Campaign::findOrFail($validated['campaign_id']);
                if ($campaign->status !== CampaignStatus::ACTIVE->value) {
                    throw new \Exception("La campagne doit être active pour recevoir des affectations.");
                }

                $position = Position::where('code', PositionCode::CP->value)->firstOrFail();

                $assignment = Assignment::create([
                    'employee_id' => $validated['employee_id'],
                    'campaign_id' => $validated['campaign_id'],
                    'position_id' => $position->id,
                    'status' => AssignmentStatus::ACTIVE->value,
                    'start_date' => $validated['start_date'],
                ]);

                $this->logHistory($assignment, 'assign', null, $validated['campaign_id'], $validated['reason']);
            });

            return redirect()->back()->with('success', 'Chef de Plateau affecté.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Affecter un SUP
     */
    public function storeSUP(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'campaign_id' => 'required|exists:campaigns,id',
            'manager_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'reason' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                // Vérifier si le SUP est déjà affecté ailleurs
                $existing = Assignment::where('employee_id', $validated['employee_id'])->where('status', 'actif')->first();
                if ($existing) {
                    throw new \Exception("Ce superviseur est déjà affecté à une campagne active.");
                }

                $position = Position::where('code', PositionCode::SUP->value)->firstOrFail();

                $assignment = Assignment::create([
                    'employee_id' => $validated['employee_id'],
                    'campaign_id' => $validated['campaign_id'],
                    'manager_id' => $validated['manager_id'],
                    'position_id' => $position->id,
                    'status' => AssignmentStatus::ACTIVE->value,
                    'start_date' => $validated['start_date'],
                ]);

                $this->logHistory($assignment, 'assign', $validated['manager_id'], $validated['campaign_id'], $validated['reason']);
            });

            return redirect()->back()->with('success', 'Superviseur affecté.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Affecter un TC
     */
    public function storeTC(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'manager_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'reason' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                // Vérifier si le TC est déjà affecté ailleurs
                $existing = Assignment::where('employee_id', $validated['employee_id'])->where('status', 'actif')->first();
                if ($existing) {
                    throw new \Exception("Ce téléconseiller est déjà affecté à une campagne active.");
                }

                // Récupérer l'affectation du manager pour hériter de la campagne
                $managerAssignment = Assignment::where('employee_id', $validated['manager_id'])
                    ->where('status', 'actif')
                    ->firstOrFail();

                $position = Position::where('code', PositionCode::TC->value)->firstOrFail();

                $assignment = Assignment::create([
                    'employee_id' => $validated['employee_id'],
                    'campaign_id' => $managerAssignment->campaign_id,
                    'manager_id' => $validated['manager_id'],
                    'position_id' => $position->id,
                    'status' => AssignmentStatus::ACTIVE->value,
                    'start_date' => $validated['start_date'],
                ]);

                $this->logHistory($assignment, 'assign', $validated['manager_id'], $managerAssignment->campaign_id, $validated['reason']);
            });

            return redirect()->back()->with('success', 'Téléconseiller affecté.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Libérer une affectation
     */
    public function release(Request $request, Assignment $assignment)
    {
        $validated = $request->validate([
            'mode' => 'required|in:solo,cascade,transfer',
            'new_manager_id' => 'nullable|exists:employees,id',
            'reason' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($assignment, $validated) {
                $mode = $validated['mode'];
                $newManagerId = $validated['new_manager_id'] ?? null;
                $reason = $validated['reason'] ?? "Libération (Mode: $mode)";

                if ($mode === 'transfer' && $newManagerId) {
                    // 1. Vérifier si le remplaçant a déjà une affectation active sur cette campagne
                    $newManagerAssignment = Assignment::where('employee_id', $newManagerId)
                        ->where('campaign_id', $assignment->campaign_id)
                        ->where('status', 'actif')
                        ->first();

                    // 2. Si non, on lui crée une nouvelle affectation du même rang
                    if (!$newManagerAssignment) {
                        $newManagerAssignment = Assignment::create([
                            'employee_id' => $newManagerId,
                            'campaign_id' => $assignment->campaign_id,
                            'position_id' => $assignment->position_id,
                            'manager_id' => $assignment->manager_id, // Il hérite du manager du sortant
                            'status' => AssignmentStatus::ACTIVE->value,
                            'start_date' => now(),
                        ]);

                        // Trace de la nouvelle affectation du remplaçant
                        $this->logHistory($newManagerAssignment, 'assign', $assignment->manager_id, $assignment->campaign_id, "Affectation automatique en tant que remplaçant");
                    }

                    // 3. Transférer tous les subordonnés au nouveau manager
                    Assignment::where('manager_id', $assignment->employee_id)
                        ->where('campaign_id', $assignment->campaign_id)
                        ->where('status', 'actif')
                        ->get()
                        ->each(function ($sub) use ($newManagerId) {
                            $oldManagerId = $sub->manager_id;
                            $sub->update(['manager_id' => $newManagerId]);
                            
                            // Trace du transfert pour chaque subordonné
                            $this->logHistory($sub, 'transfer', $newManagerId, $sub->campaign_id, "Transfert vers le nouveau responsable : " . Employee::find($newManagerId)->email);
                        });
                } elseif ($mode === 'cascade') {
                    // Libérer récursivement tous les subordonnés
                    Assignment::where('manager_id', $assignment->employee_id)
                        ->where('campaign_id', $assignment->campaign_id)
                        ->where('status', 'actif')
                        ->get()
                        ->each(function ($sub) {
                            $this->internalRelease($sub, 'cascade', null, "Libération en cascade.");
                        });
                }

                // Fermer l'affectation actuelle
                $assignment->update([
                    'status' => AssignmentStatus::COMPLETED->value,
                    'end_date' => now()
                ]);

                $this->logHistory($assignment, 'release', $assignment->manager_id, $assignment->campaign_id, $reason);
            });

            return redirect()->back()->with('success', 'Affectation libérée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Réaffectation (clôture + nouvelle pour un employé existant)
     * Utile pour changer de manager tout en gardant l'historique
     */
    public function reassign(Request $request, Assignment $assignment)
    {
        $validated = $request->validate([
            'new_manager_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'reason' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($assignment, $validated) {
                // 1. Libérer l'ancienne
                $this->internalRelease($assignment, 'solo', null, "Réaffectation : " . ($validated['reason'] ?? ''));
                
                // 2. Créer la nouvelle selon le type
                $code = $assignment->position->code;
                if ($code === 'SUP') {
                    $this->internalAssignSUP(
                        $assignment->employee_id,
                        $assignment->campaign_id,
                        $validated['new_manager_id'],
                        $validated['start_date'],
                        $validated['reason']
                    );
                } elseif ($code === 'TC') {
                    $this->internalAssignTC(
                        $assignment->employee_id,
                        $validated['new_manager_id'],
                        $validated['start_date'],
                        $validated['reason']
                    );
                }
            });

            return redirect()->back()->with('success', 'Ressource réaffectée.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Logique interne de libération (sans redirection)
     */
    private function internalRelease(Assignment $assignment, string $mode, ?int $newManagerId, ?string $reason): void
    {
        if ($mode === 'cascade') {
            Assignment::where('manager_id', $assignment->employee_id)
                ->where('campaign_id', $assignment->campaign_id)
                ->where('status', 'actif')
                ->get()
                ->each(function ($sub) {
                    $this->internalRelease($sub, 'cascade', null, "Libération en cascade.");
                });
        }

        $assignment->update([
            'status' => AssignmentStatus::COMPLETED->value,
            'end_date' => now()
        ]);

        $this->logHistory($assignment, 'release', $assignment->manager_id, $assignment->campaign_id, $reason);
    }

    /**
     * Logique interne d'assignation SUP
     */
    private function internalAssignSUP($employeeId, $campaignId, $managerId, $startDate, $reason)
    {
        $position = Position::where('code', PositionCode::SUP->value)->firstOrFail();
        $assignment = Assignment::create([
            'employee_id' => $employeeId,
            'campaign_id' => $campaignId,
            'manager_id' => $managerId,
            'position_id' => $position->id,
            'status' => AssignmentStatus::ACTIVE->value,
            'start_date' => $startDate,
        ]);
        $this->logHistory($assignment, 'assign', $managerId, $campaignId, $reason);
    }

    /**
     * Logique interne d'assignation TC
     */
    private function internalAssignTC($employeeId, $managerId, $startDate, $reason)
    {
        $managerAssignment = Assignment::where('employee_id', $managerId)
            ->where('status', 'actif')
            ->firstOrFail();

        $position = Position::where('code', PositionCode::TC->value)->firstOrFail();
        $assignment = Assignment::create([
            'employee_id' => $employeeId,
            'campaign_id' => $managerAssignment->campaign_id,
            'manager_id' => $managerId,
            'position_id' => $position->id,
            'status' => AssignmentStatus::ACTIVE->value,
            'start_date' => $startDate,
        ]);
        $this->logHistory($assignment, 'assign', $managerId, $managerAssignment->campaign_id, $reason);
    }

    /**
     * Enregistrer dans l'historique
     */
    private function logHistory(Assignment $assignment, string $action, ?int $newManagerId, int $newCampaignId, ?string $reason): void
    {
        AssignmentHistory::create([
            'assignment_id' => $assignment->id,
            'employee_id' => $assignment->employee_id,
            'new_manager_id' => $newManagerId,
            'new_campaign_id' => $newCampaignId,
            'action_type' => $action,
            'changed_by' => Auth::id(),
            'reason' => $reason,
        ]);
    }
}
