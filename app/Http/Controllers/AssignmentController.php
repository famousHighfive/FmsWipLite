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
     * Affiche la liste des affectations (non utilisé, redirigé vers campagne detail)
     */
    public function index()
    {
        return redirect()->route('campaigns.index');
    }

    /**
     * Enregistre une nouvelle affectation et crée une trace dans l'historique
     */
    public function store(Request $request)
    {
        // Validation stricte des données reçues
        // $validated = $request->validate([
        //     'employee_id' => 'required|exists:employees,id', // L'employé doit exister
        //     'campaign_id' => 'required|exists:campaigns,id', // La campagne doit exister
        //     'manager_id' => 'nullable|exists:employees,id', // Le manager (facultatif) doit être un employé existant
        //     'position_id' => 'required|exists:positions,id', // Le poste doit exister
        //     'start_date' => 'required|date', // Date de début obligatoire
        // ]);

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
                'assignment_id' => $assignment->id, // Lien vers l'affectation
                'employee_id' => $validated['employee_id'], // L'employé concerné
                'new_manager_id' => $validated['manager_id'] ?? null, // Nouveau manager si défini (en tant qu'employé)
                'new_campaign_id' => $validated['campaign_id'], // Nouvelle campagne
                'action_type' => 'assign', // Type d'action : Assignation
                'changed_by' => Auth::id(), // Utilisateur (Admin/CP) qui a fait l'action
                'reason' => $request->reason ?? 'Première affectation', // Raison ou commentaire
            ]);
        });

        // Redirection vers la page précédente avec mise à jour des données via Inertia
        return redirect()->back();
    }

    /**
     * Gère la réaffectation (changement de manager) d'une ressource
     */
    public function reassign(Request $request, Assignment $assignment)
    {
        // Validation des données de réaffectation
        $validated = $request->validate([
            'manager_id' => 'required|exists:employees,id', // Le nouveau manager doit être un employé valide
            'start_date' => 'required|date', // Date d'effet
            'reason' => 'nullable|string', // Motif optionnel
        ]);

        DB::transaction(function () use ($assignment, $validated) {
            // On sauvegarde l'ancien manager pour l'historique
            $oldManagerId = $assignment->manager_id;

            // Mise à jour de l'affectation actuelle
            $assignment->update([
                'manager_id' => $validated['manager_id'],
                'start_date' => $validated['start_date'],
            ]);

            // Enregistrement de la trace dans l'historique
            AssignmentHistory::create([
                'assignment_id' => $assignment->id,
                'employee_id' => $assignment->employee_id,
                'old_manager_id' => $oldManagerId,
                'new_manager_id' => $validated['manager_id'],
                'old_campaign_id' => $assignment->campaign_id,
                'new_campaign_id' => $assignment->campaign_id,
                'action_type' => 'transfer', // Type d'action : Transfert/Réaffectation
                'changed_by' => Auth::id(), // Utilisateur connecté
                'reason' => $validated['reason'] ?? 'Réaffectation manuelle',
            ]);
        });

        return redirect()->back();
    }

    /**
     * Gère la libération ou le transfert d'une ressource (CP, SUP ou TC)
     */
    // public function release(Request $request, Assignment $assignment)
    // {
    //     // 'mode' peut être 'solo', 'cascade' ou 'transfer'
    //     $mode = $request->mode; 
    //     $newManagerId = $request->new_manager_id; // Nécessaire uniquement pour le mode 'transfer'

    //     DB::transaction(function () use ($assignment, $mode, $newManagerId, $request) {
            
    //         // CAS 1 : TRANSFERT DE CHAINE (On change le manager de tous les subordonnés)
    //         if ($mode === 'transfer' && $newManagerId) {
    //             // On trouve tous les employés qui avaient cette personne comme manager sur cette campagne
    //             $subordinates = Assignment::where('manager_id', $assignment->employee_id)
    //                 ->where('campaign_id', $assignment->campaign_id)
    //                 ->where('status', 'actif')
    //                 ->get();

    //             foreach ($subordinates as $sub) {
    //                 $oldManager = $sub->manager_id;
    //                 // Mise à jour vers le nouveau manager
    //                 $sub->update(['manager_id' => $newManagerId]);

    //                 // On enregistre le transfert dans l'historique pour chaque subordonné
    //                 AssignmentHistory::create([
    //                     'assignment_id' => $sub->id,
    //                     'employee_id' => $sub->employee_id,
    //                     'old_manager_id' => $oldManager,
    //                     'new_manager_id' => $newManagerId,
    //                     'old_campaign_id' => $sub->campaign_id,
    //                     'new_campaign_id' => $sub->campaign_id,
    //                     'action_type' => 'transfer',
    //                     'changed_by' => Auth::id(),
    //                     'reason' => "Transfert automatique suite au départ de son manager",
    //                 ]);
    //             }
    //         } 
            
    //         // CAS 2 : LIBÉRATION EN CASCADE (On libère aussi tout le monde en dessous)
    //         elseif ($mode === 'cascade') {
    //             $subordinates = Assignment::where('manager_id', $assignment->employee_id)
    //                 ->where('campaign_id', $assignment->campaign_id)
    //                 ->where('status', 'actif')
    //                 ->get();

    //             foreach ($subordinates as $sub) {
    //                 // On marque chaque subordonné comme terminé (libéré)
    //                 $sub->update([
    //                     'status' => 'termine', 
    //                     'end_date' => now()
    //                 ]);

    //                 // Historique pour la libération de chaque subordonné
    //                 AssignmentHistory::create([
    //                     'assignment_id' => $sub->id,
    //                     'employee_id' => $sub->employee_id,
    //                     'old_manager_id' => $sub->manager_id,
    //                     'old_campaign_id' => $sub->campaign_id,
    //                     'action_type' => 'release',
    //                     'changed_by' => Auth::id(),
    //                     'reason' => "Libération en cascade suite au départ du responsable",
    //                 ]);
    //             }
    //         }

    //         // ENFIN : On libère la ressource principale (le CP ou SUP qu'on voulait retirer au départ)
    //         $assignment->update([
    //             'status' => 'termine', 
    //             'end_date' => now()
    //         ]);

    //         // Enregistrement final de la libération dans l'historique
    //         AssignmentHistory::create([
    //             'assignment_id' => $assignment->id,
    //             'employee_id' => $assignment->employee_id,
    //             'old_manager_id' => $assignment->manager_id,
    //             'old_campaign_id' => $assignment->campaign_id,
    //             'action_type' => 'release',
    //             'changed_by' => Auth::id(),
    //             'reason' => $request->reason ?? "Libération effectuée (Mode: $mode)",
    //         ]);
    //     });

    //     return redirect()->back();
    // }

    public function release(Request $request, Assignment $assignment){
        // je vais recuperer le mode choisi dans mon model
        $mode = $request->mode;

        DB::transaction(function() use ($assignment, $mode, $request){
            // 1 - gerer les subordonnées
            if($mode === 'transfer'){
                // On donne les subordonnées à un nouveau manager
                Assignment::where('manager_id', $assignment->employee_id)
                    ->where('campaign_id', $assignment->campaign_id)
                    ->where('status', 'actif')
                    ->update(['manager_id' => $request->new_manager_id]);
            }
            elseif($mode === 'cascade'){
                // j'appelle une fonction qui va descendre toute la chaîne et libérer tout le monde
                $this->libererEnCascade($assignment->employee_id, $assignment->campaign_id);
            }

            // 2 - Liberer la personne concernée
            $assignment->update([
                'status' => 'termine',
                'end_date' => now()
            ]);

            // 3 - Enregistrer l'historique de cette libération
            AssignmentHistory::create([
                'assignment_id' => $assignment->id,
                'employee_id' => $assignment->employee_id,
                'action_type' => 'release',
                'reason' => $request->reason ?? "Libération en mode $mode",
                'changed_by' => Auth::id(),
            ]);
        });
        return redirect()->back();
    }

    /**
 * Cette fonction est "récursive" : elle se répète tant qu'il y a des gens en dessous
 */
private function libererEnCascade($managerId, $campaignId)
{
    // 1. Trouver les subordonnés directs
    $subordonnes = Assignment::where('manager_id', $managerId)
        ->where('campaign_id', $campaignId)
        ->where('status', 'actif')
        ->get();

    foreach ($subordonnes as $sub) {
        // 2. Les libérer
        $sub->update(['status' => 'termine', 'end_date' => now()]);

        // 3. Chercher s'ils ont eux-mêmes des gens en dessous (ex: SUP vers TC)
        $this->libererEnCascade($sub->employee_id, $campaignId);
    }
}
}
