<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\ActivityLog; // Importation du modèle ActivityLog pour l'historique
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Importation de Auth pour récupérer l'utilisateur connecté
use Inertia\Inertia;

class CampaignController extends Controller
{
    /**
     * Liste des campagnes avec compteurs d'affectations
     */
    public function index()
    {
        // On récupère les campagnes avec le nombre d'affectations actives pour chaque campagne
        $campaigns = Campaign::withCount(['assignments' => function ($query) {
            $query->where('status', 'actif');
        }])->latest()->get();
        
        // Retourne la vue de la liste des campagnes (Image 3)
        return Inertia::render('Campaigns/Index', [
            'campaigns' => $campaigns,
        ]);
    }

    /**
     * Formulaire de création (non utilisé avec le modal PrimeVue)
     */
    public function create()
    {
        //
    }

    /**
     * Enregistrer une nouvelle campagne
     */
    public function store(Request $request)
    {
        // Validation des données entrantes
        $validated = $request->validate([
            'name' => 'required|string|max:255', // Nom obligatoire
            'description' => 'nullable|string', // Description optionnelle
            'start_date' => 'required|date', // Date de début obligatoire
            'end_date' => 'nullable|date|after_or_equal:start_date', // Date de fin doit être après ou égale au début
            'status' => 'required|in:active,inactive,terminee', // Statut limité aux valeurs définies
        ]);

        // Création de la campagne
        $campaign = Campaign::create($validated);

        // Enregistrement de l'action dans l'historique (ActivityLog)
        ActivityLog::create([
            'user_id' => Auth::id(), // ID de l'utilisateur qui crée
            'action' => 'create', // Type d'action
            'model_type' => Campaign::class, // Modèle concerné
            'model_id' => $campaign->id, // ID de la campagne créée
            'description' => "Création de la campagne : {$campaign->name}", // Description détaillée
            'ip_address' => $request->ip(), // Adresse IP de l'utilisateur
        ]);

        // Redirection vers l'index avec un message de succès
        return redirect()->back();
    }

    /**
     * Affiche le détail d'une campagne avec sa hiérarchie (Image 1)
     */
    public function show(Campaign $campaign)
    {
        // On charge les affectations avec les relations pour construire la vue hiérarchique
        $campaign->load(['assignments' => function ($query) {
            $query->where('status', 'actif')->with(['employee', 'manager', 'position']);
        }]);

        // On récupère aussi l'historique spécifique à cette campagne
        $history = \App\Models\AssignmentHistory::where('old_campaign_id', $campaign->id)
            ->orWhere('new_campaign_id', $campaign->id)
            ->with(['employee', 'author'])
            ->latest()
            ->get();

        return Inertia::render('Campaigns/Show', [
            'campaign' => $campaign,
            'assignments' => $campaign->assignments,
            'history' => $history,
            'employees' => \App\Models\Employee::where('status', 'actif')->get(),
            'positions' => \App\Models\Position::all()
        ]);
    }

    /**
     * Formulaire d'édition (non utilisé avec le modal PrimeVue)
     */
    public function edit(Campaign $campaign)
    {
        //
    }

    /**
     * Mettre à jour une campagne
     */
    public function update(Request $request, Campaign $campaign)
    {
        // Validation des données de mise à jour
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,inactive,terminee',
        ]);

        // Mise à jour de la campagne
        $campaign->update($validated);

        // Enregistrement de la modification dans l'historique
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'update',
            'model_type' => Campaign::class,
            'model_id' => $campaign->id,
            'description' => "Mise à jour de la campagne : {$campaign->name}",
            'ip_address' => $request->ip(),
        ]);

        // Retour à la page précédente
        return redirect()->back();
    }

    /**
     * Changer uniquement le statut d'une campagne
     */
    public function changeStatus(Request $request, Campaign $campaign)
    {
        // On ne réagit pas si la campagne est déjà terminée
        if ($campaign->status === 'terminee') {
            return redirect()->back()->with('error', 'Impossible de modifier le statut d\'une campagne terminée.');
        }

        // Validation du nouveau statut
        $validated = $request->validate([
            'status' => 'required|in:active,inactive', // Uniquement actif/inactif via ce bouton
        ]);

        // Sauvegarde de l'ancien statut pour la description
        $oldStatus = $campaign->status;
        
        // Mise à jour du statut
        $campaign->update(['status' => $validated['status']]);

        // Tracé spécifique pour le changement de statut (Historique)
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'status_change',
            'model_type' => Campaign::class,
            'model_id' => $campaign->id,
            'description' => "Changement de statut de la campagne {$campaign->name} : {$oldStatus} -> {$validated['status']}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->back();
    }

    /**
     * Supprimer une campagne (En réalité, on la clôture/termine)
     */
    public function destroy(Request $request, Campaign $campaign)
    {
        // On garde l'ancien statut
        $oldStatus = $campaign->status;

        // On ne supprime pas physiquement, on change le statut en 'terminee'
        $campaign->update(['status' => 'terminee']);

        // Enregistrement de la clôture (via bouton supprimer) dans l'historique
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'cloture_campagne', // Action plus explicite
            'model_type' => Campaign::class,
            'model_id' => $campaign->id,
            'description' => "Campagne clôturée : {$campaign->name}. Ancien statut: {$oldStatus}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->back();
    }
}
