<?php

use App\Http\Controllers\SubjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Emotions API routes
Route::apiResource('subjects', SubjectController::class);

// Additional emotion routes
Route::get('subjects/stats', [SubjectController::class, 'stats']);
Route::get('subjects/random', [SubjectController::class, 'random']);