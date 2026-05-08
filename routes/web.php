<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeHistoryController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\PlanningModelsController;
use App\Http\Controllers\PlanningAssignmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TimesheetController;
use App\Http\Controllers\TimesheetEntryController;
use App\Http\Controllers\ReportingController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\CampaignController;
// use App\Http\Controllers\TimesheetController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TimesheetController as ControllersTimesheetController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Redirection intelligente selon le rôle dès l'arrivée sur /
Route::get('/', function () {
    if (auth()->check()) {
        $role = auth()->user()->role?->name;

        return redirect()->route(match ($role) {
            'admin' => 'dashboard.admin',
            'cp' => 'dashboard.cp',
            'sup' => 'dashboard.sup',
            'tc' => 'dashboard.tc',
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
            'cp' => 'dashboard.cp',
            'sup' => 'dashboard.sup',
            'tc' => 'dashboard.tc',
            default => 'dashboard.tc',
        });
    })->name('dashboard');

    Route::get('/dashboard/admin', [ReportingController::class, 'admin'])->middleware('role:admin')->name('dashboard.admin');
    Route::get('/reports/export/excel', [ReportingController::class, 'exportExcel'])->name('reports.export.excel');
    Route::get('/reports/export/pdf', [ReportingController::class, 'exportPdf'])->name('reports.export.pdf');

    Route::get('/dashboard/cp', [ReportingController::class, 'chefPlateau'])->middleware('role:cp,admin')->name('dashboard.cp');

    Route::get('/dashboard/sup', [ReportingController::class, 'superviseur'])->middleware('role:sup,admin')->name('dashboard.sup');

    Route::get('/dashboard/tc', [ReportingController::class, 'teleConseiller'])->middleware('role:tc,admin')->name('dashboard.tc');

    Route::get('/employees', function () {
        return Inertia::render('Employees/Index');
    })->middleware('role:admin')->name('employees.index');
    Route::get('/users/roles', [RoleController::class, 'index'])
            ->name('roles.index');

    // --- GESTION DES PLANNINGS ---
    Route::middleware('role:cp,admin')->prefix('planning')->name('planning.')->group(function () {
        // Modèles
        Route::get('/models', [PlanningModelsController::class, 'index'])->name('models.index');
        Route::post('/models', [PlanningModelsController::class, 'store'])->name('models.store');
        Route::put('/models/{model}', [PlanningModelsController::class, 'update'])->name('models.update');
        Route::delete('/models/{model}', [PlanningModelsController::class, 'destroy'])->name('models.destroy');

        // Affectations
        Route::get('/assignments', [PlanningAssignmentController::class, 'index'])->name('assignments.index');
        Route::get('/assignments/create', [PlanningAssignmentController::class, 'create'])->name('assignments.create');
        Route::post('/assignments', [PlanningAssignmentController::class, 'store'])->name('assignments.store');
        Route::delete('/assignments/{id}', [PlanningAssignmentController::class, 'destroy'])->name('assignments.destroy');

        // Actions
        Route::post('/assignments/{id}/validate', [PlanningAssignmentController::class, 'validateAssignment'])->name('assignments.validate');
        Route::post('/assignments/bulk-validate', [PlanningAssignmentController::class, 'bulkValidate'])->name('assignments.bulk-validate');
        Route::post('/assignments/validate-all', [PlanningAssignmentController::class, 'validateAll'])->name('assignments.validate-all');
        Route::post('/assignments/{id}/suspend', [PlanningAssignmentController::class, 'suspendAssignment'])->name('assignments.suspend');
        Route::post('/assignments/{id}/terminate', [PlanningAssignmentController::class, 'terminateAssignment'])->name('assignments.terminate');

        // Pages
        Route::get('/validate', [PlanningAssignmentController::class, 'validation'])->name('validate');
        Route::get('/history', [PlanningAssignmentController::class, 'history'])->name('history');
    });

    // Routes planning pour tous les rôles (ou TC/SUP)
    Route::get('planning/mine', [PlanningAssignmentController::class, 'index'])->name('planning.mine');
    Route::get('planning/team', [PlanningAssignmentController::class, 'index'])->name('planning.team');

    // Routes pour la gestion des affectations
    Route::prefix('assignments')->name('assignments.')->group(function () {
        Route::get('/', [AssignmentController::class, 'index'])->name('index');
        Route::post('/', [AssignmentController::class, 'store'])->name('store');
        Route::get('/cp', [AssignmentController::class, 'index'])->name('cp');
        Route::get('/sup', [AssignmentController::class, 'index'])->name('sup');
        Route::get('/tc', [AssignmentController::class, 'index'])->name('tc');
        Route::get('/hierarchy', [AssignmentController::class, 'index'])->name('hierarchy');
        Route::get('/reassign', [AssignmentController::class, 'index'])->name('reassign');
        Route::get('/history', [AssignmentController::class, 'index'])->name('history');
        Route::get('/tree', [AssignmentController::class, 'index'])->name('tree');
        Route::post('/{assignment}/release', [AssignmentController::class, 'release'])->name('release');
        Route::post('/{assignment}/reassign', [AssignmentController::class, 'reassign'])->name('reassign');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/password', [ProfileController::class, 'edit'])->name('profile.password');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // Route pour enregistrer ou mettre à jour une entrée de temps
    
    Route::resource('/employees', EmployeeController::class);
    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('/assigned', [EmployeeController::class, 'index'])->name('assigned');
        Route::get('/inactifs', [EmployeeController::class, 'index'])->name('inactifs');
        Route::get('/unassigned', [EmployeeController::class, 'index'])->name('unassigned');
    });

    Route::resource('/campaigns', CampaignController::class);
    Route::prefix('campaigns')->name('campaigns.')->group(function () {
        Route::get('/active', [CampaignController::class, 'index'])->name('active');
        Route::get('/closed', [CampaignController::class, 'index'])->name('closed');
        Route::get('/details', [CampaignController::class, 'index'])->name('details');
    });
    
    Route::get('/timesheets', [TimesheetController::class, 'index'])->name('calendar.index');
    // Route::prefix('timesheets')->name('timesheets.')->group(function () {
    //     Route::get('/entry', [TimesheetController::class, 'entry'])->name('entry');
    //     Route::get('/validate', [TimesheetController::class, 'validation'])->name('validate');
    //     Route::get('/history', [TimesheetController::class, 'history'])->name('history');
    //     Route::get('/gaps', [TimesheetController::class, 'gaps'])->name('gaps');
    //     Route::get('/overtime', [TimesheetController::class, 'overtime'])->name('overtime');
    //     Route::get('/', [TimesheetController::class, 'index'])->name('index');
    //     Route::post('/', [TimesheetController::class, 'store'])->name('store');
    //     Route::get('/{timesheet}', [TimesheetController::class, 'show'])->name('show');
    //     Route::post('/{timesheet}/approve', [TimesheetController::class, 'approve'])->name('approve');
    //     Route::post('/{timesheet}/reject', [TimesheetController::class, 'reject'])->name('reject');
    // });

    Route::resource('/notifications', NotificationController::class);

    // Routes spécifiques pour les rôles (Superviseurs et Téléconseillers)
    Route::get('/supervisors', [EmployeeController::class, 'index'])->name('supervisors.index');
    Route::get('/teleconseillers', [EmployeeController::class, 'index'])->name('teleconseillers.index');

    Route::resource('/positions', PositionController::class)->only(['index', 'show']);
    Route::get('/employees/{employee}/history', [EmployeeController::class, 'history'])->name('employees.history');
    Route::post('/timesheet-entries', [TimesheetEntryController::class, 'store'])->name('timesheet-entries.store');

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


    Route::get('/dashboard/admin/stats', [ReportingController::class, 'generalStats'])
        ->middleware('role:admin')
        ->name('dashboard.admin.stats');

    Route::get('/dashboard/admin/alerts', [ReportingController::class, 'alerts'])
        ->middleware('role:admin')
        ->name('dashboard.admin.alerts');


    /*
    |--------------------------------------------------------------------------
    | REPORTS
    |--------------------------------------------------------------------------
    */

    Route::prefix('reports')->middleware('role:admin,cp,sup')->group(function () {

        Route::get('/hr', [ReportingController::class, 'hr']);

        Route::get('/campaigns', [ReportingController::class, 'campaigns']);

        Route::get('/assignments', [ReportingController::class, 'assignments']);

        Route::get('/timesheets', [ReportingController::class, 'timesheets']);

        Route::get('/analytics', [ReportingController::class, 'analytics']);

        Route::get('/kpis', [ReportingController::class, 'kpis']);

        Route::get('/team', [ReportingController::class, 'team']);

        Route::get('/productivity', [ReportingController::class, 'productivity']);

        Route::get('/export/pdf', [ReportingController::class, 'exportPdf']);

        Route::get('/export/excel', [ReportingController::class, 'exportExcel']);
    });

});
Route::post('/timesheet-entries', [TimesheetEntryController::class, 'store']);
Route::post('/timesheets/{timesheet}/submit', [TimesheetController::class, 'submit'])->name('timesheets.submit');


require __DIR__.'/auth.php';
