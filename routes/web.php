<?php

use App\Http\Controllers\CommetnController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostController;
use App\Models\Commetn;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::resource('posts', PostController::class);

Route::resource('comments', CommetnController::class)->parameters([
    'comments' => 'commetn'
]);

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
