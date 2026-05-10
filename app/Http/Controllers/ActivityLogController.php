<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Inertia\Inertia;

class ActivityLogController extends Controller
{
    public function index()
    {
        // On récupère les 100 derniers logs pour le filtrage automatique côté client (PrimeVue)
        // comme demandé par l'utilisateur pour éviter les requêtes à chaque caractère.
        return Inertia::render('ActivityLogs/Index', [
            'logs' => ActivityLog::with('user')
                ->latest()
                ->take(100)
                ->get()
        ]);
    }
}
