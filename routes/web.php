<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TimesheetController;
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

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/calendar', [TimesheetController::class, 'index'])->name('calendar.index');
});

require __DIR__.'/auth.php';
