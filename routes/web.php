<?php

use App\Http\Controllers\CampaignController;
use App\Http\Controllers\PlanningModelsController;
use App\Http\Controllers\ProfileController;
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

    // Routes pour la gestion des utilisateurs
    Route::resource('users', UserController::class);
    Route::resource('users', UserController::class)->middleware('role:admin');
    Route::resource('planning/models', PlanningModelsController::class)->middleware('role:admin')->names('planning.models');

    // Routes pour la gestion des affectations
    Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments.index');
    Route::post('/assignments', [AssignmentController::class, 'store'])->name('assignments.store');
    Route::post('/assignments/{assignment}/release', [AssignmentController::class, 'release'])->name('assignments.release');
    Route::post('/assignments/{assignment}/reassign', [AssignmentController::class, 'reassign'])->name('assignments.reassign');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    // campaigns route
    Route::resource('campaigns', CampaignController::class);
    Route::get('/active/campaigns', [CampaignController::class, 'active'])->name('active');
    Route::get('/inactive/campaigns', [CampaignController::class, 'inactive'])->name('inactive');
    Route::post(
        '/assign/{assignment}/campaign',
        [AssignmentController::class, 'assignNewCampaign']
    )->name('assignments.assignCampaign');
    Route::patch('/campaigns/{campaign}/status', [CampaignController::class, 'changeStatus'])->name('campaigns.status');

    // assignments route
    Route::resource('assignments', AssignmentController::class);
    Route::get('/assign/cp', [AssignmentController::class, 'assignCP'])->name('assign.cp');
    Route::post('/assign/cp', [AssignmentController::class, 'storeCP'])
        ->name('assign.cp.store');
    Route::get('/assign/sup', [AssignmentController::class, 'assignSUP'])
        ->name('assign.sup');
    Route::post('/assign/sup', [AssignmentController::class, 'storeSUP'])
        ->name('assign.sup.store');
    Route::get(
        '/assign/tc',
        [AssignmentController::class, 'assignTC']
    )->name('assign.tc');
    Route::post(
        '/assign/tc',
        [AssignmentController::class, 'storeTC']
    )->name('assign.tc.store');


/**
 * =========================================
 * LIBÉRATION
 * =========================================
 */
Route::post(
    '/assignments/{assignment}/release',
    [AssignmentController::class, 'release']
)->name('assignments.release');

/**
 * =========================================
 * RÉAFFECTATION
 * =========================================
 */
Route::post(
    '/assignments/{assignment}/reassign',
    [AssignmentController::class, 'reassign']
)->name('assignments.reassign');
});


require __DIR__ . '/auth.php';
