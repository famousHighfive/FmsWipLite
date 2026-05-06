<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TimesheetController;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard/TeleConseiller');
    })->name('dashboard');

    Route::get('/dashboard/admin', function () {
        return Inertia::render('Dashboard/Admin');
    })->name('dashboard.admin');

    Route::get('/dashboard/cp', function () {
        return Inertia::render('Dashboard/ChefPlateau');
    })->name('dashboard.cp');

    Route::get('/dashboard/sup', function () {
        return Inertia::render('Dashboard/Superviseur');
    })->name('dashboard.sup');

    Route::get('/dashboard/tc', function () {
        return Inertia::render('Dashboard/TeleConseiller');
    })->name('dashboard.tc');

    Route::get('/employees', function () {
        return Inertia::render('Employees/Index');
    })->name('employees.index');

    Route::resource('users', UserController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/calendar', [TimesheetController::class, 'index'])->name('calendar.index');
});

require __DIR__.'/auth.php';
