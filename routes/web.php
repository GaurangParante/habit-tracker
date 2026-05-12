<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HabitController;
use App\Http\Controllers\HabitHistoryController;
use App\Http\Controllers\HabitLogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\TodoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics');
    Route::get('/habits/manage', [HabitController::class, 'manage'])->name('habits.manage');
    Route::get('/habit/history', [HabitHistoryController::class, 'index'])->name('habits.history');
    Route::resource('habits', HabitController::class);
    Route::post('/habits/toggle', [HabitLogController::class, 'toggle'])->name('habits.toggle');
    Route::post('/habit/toggle', [HabitLogController::class, 'toggle'])->name('habit.toggle');

    Route::get('/api/habits/{id}/weekly', [HabitLogController::class, 'weekly'])->name('habits.weekly');
    Route::get('/api/habits/{id}/monthly', [HabitLogController::class, 'monthly'])->name('habits.monthly');

    Route::get('/todos', [TodoController::class, 'index'])->name('todos.index');
    Route::post('/todos', [TodoController::class, 'store'])->name('todos.store');
    Route::put('/todos/{id}', [TodoController::class, 'update'])->name('todos.update');
    Route::delete('/todos/{id}', [TodoController::class, 'destroy'])->name('todos.destroy');
    Route::post('/todos/toggle', [TodoController::class, 'toggleStatus'])->name('todos.toggle');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
