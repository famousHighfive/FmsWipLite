<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Assignment;
use App\Models\PlanningModel;
use App\Models\PlanningAssignment;
use App\Models\PlanningHistory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

use App\Models\Campaign;
use App\Models\Position;

class PlanningAssignmentController extends Controller
{
    public function index()
    {
        $allAssignments = PlanningAssignment::with(['employee.user.role', 'planningModel', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();

        $supervisors = Employee::with('user.role')
            ->whereHas('user', function ($query) {
                $query->whereHas('role', function ($q) {
                    $q->where('name', 'sup');
                });
            })
            ->get();

        // Récupérer les chefs de plateau pour l'affectation des superviseurs
        $chefsDePlateau = Employee::whereHas('user.role', function($q) {
            $q->where('name', 'cp');
        })->get();

        // Récupérer le position_id pour SUP
        $supPosition = Position::where('code', 'SUP')->first();

        $supervisorAssignments = $supervisors->map(function ($supervisor) use ($allAssignments) {
            $supervisorAssignments = $allAssignments->where('employee_id', $supervisor->id);

            // Vérifier si le superviseur a une affectation de campagne active
            $campaignAssignment = Assignment::with('campaign')
                ->where('employee_id', $supervisor->id)
                ->where('status', 'actif')
                ->first();

            $teleconseillerIds = Assignment::where('manager_id', $supervisor->id)
                ->where('status', 'actif')
                ->pluck('employee_id');

            $teleconseillerAssignments = $allAssignments->whereIn('employee_id', $teleconseillerIds);

            return [
                'supervisor' => [
                    'id' => $supervisor->id,
                    'name' => $supervisor->user->name,
                    'has_campaign' => $campaignAssignment !== null,
                    'campaign_name' => $campaignAssignment?->campaign?->name,
                ],
                'assignments' => $supervisorAssignments->map(fn($a) => [
                    'id' => $a->id,
                    'model' => ['name' => $a->planningModel?->name ?? 'N/A'],
                    'start_date' => $a->start_date ? $a->start_date->format('d/m/Y') : 'N/A',
                    'end_date' => $a->end_date ? $a->end_date->format('d/m/Y') : 'N/A',
                    'status' => $a->status,
                ]),
                'teleconseillers' => $teleconseillerAssignments->map(fn($a) => [
                    'id' => $a->id,
                    'employee' => [
                        'name' => $a->employee?->user?->name ?? 'N/A',
                        'role' => $a->employee?->user?->role?->name ?? 'N/A',
                    ],
                    'model' => ['name' => $a->planningModel?->name ?? 'N/A'],
                    'start_date' => $a->start_date ? $a->start_date->format('d/m/Y') : 'N/A',
                    'end_date' => $a->end_date ? $a->end_date->format('d/m/Y') : 'N/A',
                    'status' => $a->status,
                ]),
            ];
        });

        return Inertia::render('Planning/Assignments/Index', [
            'supervisorAssignments' => $supervisorAssignments,
            'campaigns' => Campaign::where('status', 'active')->get(),
            'chefsDePlateau' => $chefsDePlateau,
            'supPositionId' => $supPosition?->id
        ]);
    }

    public function create()
    {
        return Inertia::render('Planning/Assignments/Create', [
            'supervisors' => Employee::with('user.role')
                            ->whereHas('user', function ($query) {
                                $query->whereHas('role', function ($q) {
                                    $q->where('name', 'sup');
                                });
                            })
                            ->get()
                            ->map(fn($emp) => [
                                'id' => $emp->id,
                                'name' => $emp->user->name,
                                'role' => $emp->user->role->name,
                            ]),
            'models' => PlanningModel::all(['id', 'name', 'total_hours'])
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'supervisor_id' => 'required|exists:employees,id',
            'planning_model_id' => 'required|exists:planning_models,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ]);

        $supervisor = Employee::findOrFail($request->supervisor_id);

        $teleconseillerIds = Assignment::where('manager_id', $supervisor->id)
            ->where('status', 'actif')
            ->pluck('employee_id');

        $employeesToAssign = collect([$supervisor])->merge(Employee::whereIn('id', $teleconseillerIds)->get());

        foreach ($employeesToAssign as $employee) {
            $exists = PlanningAssignment::where('employee_id', $employee->id)
                ->where(function ($query) use ($request) {
                    $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                          ->orWhereBetween('end_date', [$request->start_date, $request->end_date]);
                })->exists();

            if (!$exists) {
                $assignment = PlanningAssignment::create([
                    'employee_id' => $employee->id,
                    'planning_model_id' => $request->planning_model_id,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'created_by' => Auth::id(),
                    'status' => 'en attente',
                ]);

                PlanningHistory::create([
                    'planning_assignment_id' => $assignment->id,
                    'old_status' => '',
                    'new_status' => 'en attente',
                    'changed_by' => Auth::id(),
                    'reason' => 'Création de l\'affectation',
                ]);
            }
        }

        return redirect()->route('planning.assignments.index')
            ->with('success', 'Affectations créées avec succès. En attente de validation.');
    }

    public function validateAssignment(Request $request, $id)
    {
        $assignment = PlanningAssignment::findOrFail($id);
        $oldStatus = $assignment->status;

        $assignment->update([
            'status' => 'validé',
            'validated_at' => now(),
            'validated_by' => Auth::id()
        ]);

        PlanningHistory::create([
            'planning_assignment_id' => $assignment->id,
            'old_status' => $oldStatus,
            'new_status' => 'validé',
            'changed_by' => Auth::id(),
            'reason' => 'Validation du planning',
        ]);

        return back()->with('success', 'Le planning est désormais effectif.');
    }

    public function bulkValidate(Request $request)
    {
        $ids = $request->input('ids', []);

        $assignments = PlanningAssignment::whereIn('id', $ids)
            ->where('status', 'en attente')
            ->get();

        foreach ($assignments as $assignment) {
            $oldStatus = $assignment->status;

            $assignment->update([
                'status' => 'validé',
                'validated_at' => now(),
                'validated_by' => Auth::id()
            ]);

            PlanningHistory::create([
                'planning_assignment_id' => $assignment->id,
                'old_status' => $oldStatus,
                'new_status' => 'validé',
                'changed_by' => Auth::id(),
                'reason' => 'Validation en lot',
            ]);
        }

        return back()->with('success', count($assignments) . ' plannings ont été validés.');
    }

    public function validateAll()
    {
        $assignments = PlanningAssignment::where('status', 'en attente')->get();

        foreach ($assignments as $assignment) {
            $oldStatus = $assignment->status;

            $assignment->update([
                'status' => 'validé',
                'validated_at' => now(),
                'validated_by' => Auth::id()
            ]);

            PlanningHistory::create([
                'planning_assignment_id' => $assignment->id,
                'old_status' => $oldStatus,
                'new_status' => 'validé',
                'changed_by' => Auth::id(),
                'reason' => 'Validation de tous les plannings',
            ]);
        }

        return back()->with('success', count($assignments) . ' plannings ont été validés.');
    }

    public function suspendAssignment(Request $request, $id)
    {
        $assignment = PlanningAssignment::findOrFail($id);
        $oldStatus = $assignment->status;

        $assignment->update([
            'status' => 'suspendu',
        ]);

        PlanningHistory::create([
            'planning_assignment_id' => $assignment->id,
            'old_status' => $oldStatus,
            'new_status' => 'suspendu',
            'changed_by' => Auth::id(),
            'reason' => $request->input('reason', 'Suspension du planning'),
        ]);

        return back()->with('success', 'Le planning a été suspendu.');
    }

    public function terminateAssignment($id)
    {
        $assignment = PlanningAssignment::findOrFail($id);
        $oldStatus = $assignment->status;

        $assignment->update([
            'status' => 'terminé',
        ]);

        PlanningHistory::create([
            'planning_assignment_id' => $assignment->id,
            'old_status' => $oldStatus,
            'new_status' => 'terminé',
            'changed_by' => Auth::id(),
            'reason' => 'Terminaison du planning',
        ]);

        return back()->with('success', 'Le planning a été terminé.');
    }

    public function validation()
    {
        $pendingAssignments = PlanningAssignment::with(['employee.user.role', 'planningModel'])
            ->where('status', 'en attente')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($a) => [
                'id' => $a->id,
                'employee' => [
                    'name' => $a->employee->user->name,
                    'role' => $a->employee->user->role?->name ?? 'N/A',
                ],
                'model' => ['name' => $a->planningModel->name],
                'start_date' => $a->start_date->format('d/m/Y'),
                'end_date' => $a->end_date->format('d/m/Y'),
                'status' => $a->status,
            ]);

        return Inertia::render('Planning/Assignments/Validation', [
            'pendingAssignments' => $pendingAssignments
        ]);
    }

    public function history()
    {
        $history = PlanningHistory::with(['planningAssignment.employee.user', 'changedBy'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($h) => [
                'id' => $h->id,
                'planning_assignment' => [
                    'employee_name' => $h->planningAssignment->employee->user->name,
                ],
                'old_status' => $h->old_status,
                'new_status' => $h->new_status,
                'changed_by' => $h->changedBy->name ?? 'Système',
                'reason' => $h->reason,
                'created_at' => $h->created_at->format('d/m/Y H:i'),
            ]);

        return Inertia::render('Planning/Assignments/History', [
            'history' => $history
        ]);
    }

    public function destroy($id)
    {
        $assignment = PlanningAssignment::findOrFail($id);
        $assignment->delete();

        return back()->with('success', 'Affectation supprimée.');
    }
}
