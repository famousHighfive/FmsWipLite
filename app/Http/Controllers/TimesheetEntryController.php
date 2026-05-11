<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTimesheetEntryRequest;
use App\Http\Requests\UpdateTimesheetEntryRequest;
use App\Models\Timesheet;
use App\Models\TimesheetEntry;
use Carbon\Carbon;
use Inertia\Inertia;

class TimesheetEntryController extends Controller
{
    /**
     * LISTE DES ENTRÉES
     * Récupère toutes les saisies avec leur feuille de temps parente.
     */
    public function index()
    {
        $entries = TimesheetEntry::with(['timesheet'])->get();
        return Inertia::render('Timesheets/TimesCard', [
            'entries' => $entries
        ]);
    }

    /**
     * ENREGISTREMENT DES HEURES (INDIVIDUEL OU GROUPÉ)
     * Calcule la durée de travail et met à jour les entrées pour un ou plusieurs employés.
     */
    public function store(StoreTimesheetEntryRequest $request)
    {
        $validated = $request->validated();

        // On récupère le tableau d'IDs (bulk) ou l'ID unique envoyé par le frontend
        $timesheetIds = $request->input('timesheet_ids', [$validated['timesheet_id']]);

        if (empty($timesheetIds)) {
            return back()->withErrors(['message' => 'Aucune feuille de temps sélectionnée.']);
        }

        // --- TRAITEMENT PAR FEUILLE DE TEMPS ---
        foreach ($timesheetIds as $tsId) {
            $timesheet = Timesheet::findOrFail($tsId);

            // Sécurité : On ignore le traitement si la feuille est déjà verrouillée (soumis)
            if ($timesheet->status === 'soumis') {
                continue;
            }

            // --- CALCUL DE LA DURÉE DE TRAVAIL ---
            $totalHours = 0;
            if ($validated['check_in'] && $validated['check_out']) {
                $start = Carbon::parse($validated['check_in']);
                $end = Carbon::parse($validated['check_out']);
                
                // Calcul : (Fin - Début - Pause) converti en heures décimales
                $diffInMinutes = $start->diffInMinutes($end);
                $totalHours = max(0, ($diffInMinutes - ($validated['break_duration'] ?? 0)) / 60);
            }

            // --- MISE À JOUR OU CRÉATION (UPSERT) ---
            // Si une entrée existe déjà pour ce jour et cette feuille, on la met à jour, sinon on la crée.
            TimesheetEntry::updateOrCreate(
                [
                    'timesheet_id' => $tsId, 
                    'date'         => $validated['date']
                ],
                [
                    'check_in'       => $validated['check_in'],
                    'check_out'      => $validated['check_out'],
                    'break_duration' => $validated['break_duration'] ?? 0,
                    'total_hours'    => $totalHours,
                    'planned_hours'  => 7.0, // Valeur par défaut (à rendre dynamique si besoin)
                    'overtime_hours' => $totalHours - 7.0, // Calcul des heures supplémentaires
                    'comment'        => $validated['comment'] ?? null
                ]
            );

            // --- MISE À JOUR DU STATUT PARENT ---
            // Dès qu'une heure est saisie, la feuille passe de 'brouillon' à 'valide' (prête à être soumise)
            if ($timesheet->status === 'brouillon') {
                $timesheet->update(['status' => 'valide']);
            }
        }

        return back(); // Retourne au calendrier avec les données actualisées
    }

    /**
     * RÉCUPÉRATION AJAX D'UNE ENTRÉE
     * Utile pour charger les données d'un jour précis pour un employé.
     */
    public function show($employeeId, $date)
    {
        $entry = TimesheetEntry::whereHas('timesheet', function ($query) use ($employeeId) {
                $query->where('employee_id', $employeeId);
            })
            ->where('date', $date)
            ->first();

        return response()->json($entry);
    }

    /**
     * SUPPRESSION D'UNE ENTRÉE
     * Permet de vider une case du calendrier.
     */
    public function destroy(TimesheetEntry $entry)
    {
        $timesheet = $entry->timesheet;

        // On ne peut supprimer que si la semaine n'est pas verrouillée
        if ($timesheet->status !== 'soumis') {
            $entry->delete();
        }

        return back();
    }
}
