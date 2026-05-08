<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Inertia\Inertia;

class ActivityLogController extends Controller
{
    public function index()
    {
        return Inertia::render('ActivityLogs/Index', [
            'logs' => ActivityLog::with('user')
                ->latest()
                ->paginate(10)
                
        ]);
    }
}
