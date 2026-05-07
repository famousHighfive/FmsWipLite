<?php

use App\Http\Controllers\PlanningAssignmentController;
use App\Http\Controllers\PlanningModelsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
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
    Route::post('planning/assignments/{id}/suspend', [PlanningAssignmentController::class, 'suspendAssignment'])->middleware('role:cp,admin')->name('planning.assignments.suspend');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
