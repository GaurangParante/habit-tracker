<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HabitController;
use App\Http\Controllers\HabitLogController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('habits', HabitController::class);
    Route::post('/habits/toggle', [HabitLogController::class, 'toggle'])->name('habits.toggle');
    Route::post('/habit/toggle', [HabitLogController::class, 'toggle'])->name('habit.toggle');

    Route::get('/api/habits/{id}/weekly', [HabitLogController::class, 'weekly'])->name('habits.weekly');
    Route::get('/api/habits/{id}/monthly', [HabitLogController::class, 'monthly'])->name('habits.monthly');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
