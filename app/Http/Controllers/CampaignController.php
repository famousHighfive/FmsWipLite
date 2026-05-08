<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Assignment;
use App\Models\ActivityLog;
use App\Http\Requests\Campaigns\StoreCampaignRequest;
use App\Enums\CampaignStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CampaignController extends Controller
{
    /**
     * Liste des campagnes avec pagination et filtres
     */
    public function index(Request $request)
    {
        $query = Campaign::withCount(['assignments' => function ($query) {
            $query->where('status', 'actif');
        }]);

        // Filtre par recherche (nom)
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filtre par statut
        if ($request->status && $request->status !== 'Tous') {
            $query->where('status', strtolower($request->status));
        }

        $campaigns = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Campaigns/Index', [
            'campaigns' => $campaigns,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Enregistrer une nouvelle campagne
     */
    public function store(StoreCampaignRequest $request)
    {
        // On valide les données via le FormRequest
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            // Création de la campagne avec le statut par défaut 'inactive'
            $campaign = Campaign::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'] ?? null,
                'status' => CampaignStatus::INACTIVE->value,
            ]);

            // Trace de l'opération
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'create',
                'model_type' => Campaign::class,
                'model_id' => $campaign->id,
                'description' => "Création de la campagne : {$campaign->name}",
                'ip_address' => request()->ip(),
            ]);
        });

        return redirect()->route('campaigns.index')
            ->with('success', 'Campagne créée avec succès.');
    }

    /**
     * Voir le détail d'une campagne
     */
    public function show(Campaign $campaign)
    {
        // Eager loading des affectations actives et de la hiérarchie
        $campaign->load(['assignments' => function ($query) {
            $query->where('status', 'actif')->with(['employee', 'manager', 'position']);
        }]);

        // Récupération de l'historique des 20 derniers mouvements
        $history = \App\Models\AssignmentHistory::where('new_campaign_id', $campaign->id)
            ->orWhere('old_campaign_id', $campaign->id)
            ->with(['employee', 'author'])
            ->latest()
            ->take(20)
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
     * Mettre à jour une campagne
     */
    public function update(StoreCampaignRequest $request, Campaign $campaign)
    {
        // Une campagne terminée ne peut plus être modifiée
        if ($campaign->status === CampaignStatus::COMPLETED->value) {
            return redirect()->back()->with('error', "Une campagne terminée ne peut plus être modifiée.");
        }

        $validated = $request->validated();

        DB::transaction(function () use ($campaign, $validated) {
            $campaign->update($validated);

            // Trace de l'opération
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'update',
                'model_type' => Campaign::class,
                'model_id' => $campaign->id,
                'description' => "Mise à jour de la campagne : {$campaign->name}",
                'ip_address' => request()->ip(),
            ]);
        });

        return redirect()->back()->with('success', 'Campagne mise à jour.');
    }

    /**
     * Supprimer une campagne
     */
    public function destroy(Campaign $campaign)
    {
        // Une campagne active ne peut pas être supprimée
        if ($campaign->status === CampaignStatus::ACTIVE->value) {
            return redirect()->back()->with('error', "Une campagne active ne peut pas être supprimée.");
        }

        DB::transaction(function () use ($campaign) {
            $name = $campaign->name;
            $id = $campaign->id;
            
            $campaign->delete();

            // Trace de l'opération
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'delete',
                'description' => "Suppression de la campagne : {$name} (ID: {$id})",
                'ip_address' => request()->ip(),
            ]);
        });

        return redirect()->route('campaigns.index')->with('success', 'Campagne supprimée.');
    }

    /**
     * Activer une campagne
     */
    public function activate(Campaign $campaign)
    {
        $this->changeStatus($campaign, CampaignStatus::ACTIVE);
        return redirect()->back()->with('success', 'Campagne activée.');
    }

    /**
     * Désactiver une campagne
     */
    public function deactivate(Campaign $campaign)
    {
        $this->changeStatus($campaign, CampaignStatus::INACTIVE);
        return redirect()->back()->with('success', 'Campagne désactivée.');
    }

    /**
     * Clôturer une campagne
     */
    public function complete(Campaign $campaign)
    {
        $this->changeStatus($campaign, CampaignStatus::COMPLETED);
        return redirect()->back()->with('success', 'Campagne terminée et affectations clôturées.');
    }

    /**
     * Logique privée de changement de statut
     */
    private function changeStatus(Campaign $campaign, CampaignStatus $status)
    {
        DB::transaction(function () use ($campaign, $status) {
            $oldStatus = $campaign->status;
            $campaign->update(['status' => $status->value]);

            // Si on clôture la campagne, on ferme toutes les affectations actives
            if ($status === CampaignStatus::COMPLETED) {
                Assignment::where('campaign_id', $campaign->id)
                    ->where('status', 'actif')
                    ->update([
                        'status' => 'termine',
                        'end_date' => now()
                    ]);
            }

            // Trace de l'opération
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'status_change',
                'model_type' => Campaign::class,
                'model_id' => $campaign->id,
                'description' => "Changement de statut : {$oldStatus} -> {$status->value}",
                'ip_address' => request()->ip(),
            ]);
        });
    }
}
