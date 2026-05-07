<?php

use App\Http\Controllers\PlanningAssignmentController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\PlanningModelsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportingController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssignmentController; // Importation du contrôleur d'affectations
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Redirection intelligente selon le rôle dès l'arrivée sur /
Route::get('/', function () {
    if (auth()->check()) {
        $role = auth()->user()->role?->name;
        return redirect()->route(match ($role) {
            'admin' => 'dashboard.admin',
            'cp'    => 'dashboard.cp',
            'sup'   => 'dashboard.sup',
            'tc'    => 'dashboard.tc',
            default => 'dashboard.tc',
        });
    }
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // Route /dashboard redirige aussi selon le rôle (évite le bug TeleConseiller pour CP)
    Route::get('/dashboard', function () {
        $role = auth()->user()->role?->name;
        return redirect()->route(match ($role) {
            'admin' => 'dashboard.admin',
            'cp'    => 'dashboard.cp',
            'sup'   => 'dashboard.sup',
            'tc'    => 'dashboard.tc',
            default => 'dashboard.tc',
        });
    })->name('dashboard');

    Route::get('/dashboard/admin', function () {
        return Inertia::render('Dashboard/Admin');
    })->middleware('role:admin')->name('dashboard.admin');

    Route::get('/dashboard/cp', function () {
        return Inertia::render('Dashboard/ChefPlateau');
    })->middleware('role:cp,admin')->name('dashboard.cp');

    Route::get('/dashboard/sup', function () {
        return Inertia::render('Dashboard/Superviseur');
    })->middleware('role:sup,admin')->name('dashboard.sup');

    Route::get('/dashboard/tc', function () {
        return Inertia::render('Dashboard/TeleConseiller');
    })->middleware('role:tc,admin')->name('dashboard.tc');

    Route::get('/employees', function () {
        return Inertia::render('Employees/Index');
    })->middleware('role:admin')->name('employees.index');
    Route::get('/users/roles', [RoleController::class, 'index'])
            ->name('roles.index');

    // Routes pour la gestion des utilisateurs
    Route::resource('users', UserController::class);
    Route::resource('users', UserController::class)->middleware('role:admin');

    // Planning Models
    Route::resource('planning/models', PlanningModelsController::class)->middleware('role:cp,admin')->names('planning.models');

    // Planning Assignments
    Route::resource('planning/assignments', PlanningAssignmentController::class)->middleware('role:cp,admin')->names('planning.assignments');

    // Additional planning routes that match sidebar
    Route::get('planning/validate', [PlanningAssignmentController::class, 'validation'])->middleware('role:cp,admin')->name('planning.validate');
    Route::get('planning/history', [PlanningAssignmentController::class, 'history'])->middleware('role:cp,admin')->name('planning.history');
    Route::post('planning/assignments/{id}/validate', [PlanningAssignmentController::class, 'validateAssignment'])->middleware('role:cp,admin')->name('planning.assignments.validate');
    Route::post('planning/assignments/bulk-validate', [PlanningAssignmentController::class, 'bulkValidate'])->middleware('role:cp,admin')->name('planning.assignments.bulk-validate');
    Route::post('planning/assignments/validate-all', [PlanningAssignmentController::class, 'validateAll'])->middleware('role:cp,admin')->name('planning.assignments.validate-all');
    Route::post('planning/assignments/{id}/suspend', [PlanningAssignmentController::class, 'suspendAssignment'])->middleware('role:cp,admin')->name('planning.assignments.suspend');
    Route::post('planning/assignments/{id}/terminate', [PlanningAssignmentController::class, 'terminateAssignment'])->middleware('role:cp,admin')->name('planning.assignments.terminate');

    // Routes pour la gestion des affectations
    Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments.index');
    Route::post('/assignments', [AssignmentController::class, 'store'])->name('assignments.store');
    Route::post('/assignments/{assignment}/release', [AssignmentController::class, 'release'])->name('assignments.release');
    Route::post('/assignments/{assignment}/reassign', [AssignmentController::class, 'reassign'])->name('assignments.reassign');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::middleware(['role:admin'])->group(function () {

        // USERS
        Route::resource('users', UserController::class);

        // ROLES & PERMISSIONS

        Route::post('/users/roles', [RoleController::class, 'store'])
            ->name('roles.store');

        Route::put('/users/roles/{role}', [RoleController::class, 'update'])
            ->name('roles.update');

        Route::delete('/users/roles/{role}', [RoleController::class, 'destroy'])
            ->name('roles.destroy');

        // ACTIVITY LOGS
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])
            ->name('activity-logs.index');

        // // SETTINGS
        // Route::get('/settings', [SettingController::class, 'index'])
        //     ->name('settings.index');

        // Route::put('/settings', [SettingController::class, 'update'])
        //     ->name('settings.update');
    });

    /*
    |--------------------------------------------------------------------------
    | DASHBOARDS
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard/admin', [ReportingController::class, 'admin'])
        ->middleware('role:admin')
        ->name('dashboard.admin');

    Route::get('/dashboard/admin/stats', [ReportingController::class, 'generalStats'])
        ->middleware('role:admin')
        ->name('dashboard.admin.stats');

    Route::get('/dashboard/admin/alerts', [ReportingController::class, 'alerts'])
        ->middleware('role:admin')
        ->name('dashboard.admin.alerts');

    Route::get('/dashboard/cp', [ReportingController::class, 'cp'])
        ->middleware('role:cp,admin')
        ->name('dashboard.cp');

    Route::get('/dashboard/sup', [ReportingController::class, 'sup'])
        ->middleware('role:sup,admin')
        ->name('dashboard.sup');

    Route::get('/dashboard/tc', [ReportingController::class, 'tc'])
        ->middleware('role:tc,admin')
        ->name('dashboard.tc');

    /*
    |--------------------------------------------------------------------------
    | REPORTS
    |--------------------------------------------------------------------------
    */

    Route::prefix('reports')->middleware('role:admin')->group(function () {

        Route::get('/hr', [ReportingController::class, 'hr']);

        Route::get('/campaigns', [ReportingController::class, 'campaigns']);

        Route::get('/assignments', [ReportingController::class, 'assignments']);

        Route::get('/timesheets', [ReportingController::class, 'timesheets']);

        Route::get('/analytics', [ReportingController::class, 'analytics']);

        Route::get('/kpis', [ReportingController::class, 'kpis']);

        Route::get('/export/pdf', [ReportingController::class, 'exportPdf']);

        Route::get('/export/excel', [ReportingController::class, 'exportExcel']);
    });

});

Route::middleware('auth')->group(function () {
    Route::resource('campaigns', CampaignController::class);

    // route specifique pour le statut
    Route::patch('/campaigns/{campaign}/status', [CampaignController::class, 'changeStatus'])->name('campaigns.status');
});

require __DIR__.'/auth.php';
