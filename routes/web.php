<?php

use App\Http\Controllers\CampaignController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
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
    // Dashboard
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard/TeleConseiller');
    })->name('dashboard');

    // Campagnes (RESTful)
    Route::resource('campaigns', CampaignController::class);
    
    // Cycle de vie des campagnes
    Route::patch('/campaigns/{campaign}/activate', [CampaignController::class, 'activate'])->name('campaigns.activate');
    Route::patch('/campaigns/{campaign}/deactivate', [CampaignController::class, 'deactivate'])->name('campaigns.deactivate');
    Route::patch('/campaigns/{campaign}/complete', [CampaignController::class, 'complete'])->name('campaigns.complete');

    // Affectations
    Route::prefix('affectations')->name('affectations.')->group(function () {
        Route::post('/cp', [AssignmentController::class, 'storeCP'])->name('store.cp');
        Route::post('/sup', [AssignmentController::class, 'storeSUP'])->name('store.sup');
        Route::post('/tc', [AssignmentController::class, 'storeTC'])->name('store.tc');
        Route::post('/{assignment}/release', [AssignmentController::class, 'release'])->name('release');
        Route::post('/{assignment}/reassign', [AssignmentController::class, 'reassign'])->name('reassign');
        Route::get('/history', [AssignmentController::class, 'history'])->name('history');
    });

    // Employés et Utilisateurs
    Route::get('/employees', function () {
        return Inertia::render('Employees/Index');
    })->name('employees.index');
    Route::resource('users', UserController::class);

    // Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
