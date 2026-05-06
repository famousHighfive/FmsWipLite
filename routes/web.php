<?php

use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ProfileController;
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

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Routes provisoires pour voir le rendu de chaque interface
Route::get("/dashboard/admin", function () {
    return Inertia::render('Dashboard/Admin');
});
Route::get("/employees", function () {
    return Inertia::render('Employees/Index');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::resource('campaigns', CampaignController::class);

    // route specifique pour le statut
    Route::patch('/campaigns/{campaign}/status', [CampaignController::class, 'changeStatus'])->name('campaigns.status');
});

require __DIR__.'/auth.php';
