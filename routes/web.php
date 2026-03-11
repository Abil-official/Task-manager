<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
});
Route::controller(TaskController::class)->prefix('tasks')->as('tasks.')->group(function () {
    Route::get('/export', 'exportTask')->name('export-task');
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{id}', 'show')->name('show');
    Route::get('/{id}/edit', 'edit')->name('edit');
    Route::put('/{id}/edit', 'update')->name('update');
    Route::put('/{id}/update-status', 'updateTaskStatus')->name('update-status');
})->middleware('throttle:10,1');

require __DIR__.'/settings.php';
