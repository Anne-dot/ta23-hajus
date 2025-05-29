<?php

use App\Http\Controllers\CommetnController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MarkerController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SubjectController;
use App\Models\Commetn;
use App\Models\Marker;
use App\Models\MyFavoriteSubject;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Mockery\Matcher\Subset;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::resource('markers', MarkerController::class)
    ->except(['index'])
    ->middleware(['auth', 'verified']);

Route::get('/posts', [PostController::class, 'index'])->name('posts.index');

Route::middleware(['auth'])->group(function () {
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
});

Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');

Route::resource('comments', CommetnController::class)->parameters([
    'comments' => 'commetn'
]);

Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects.index');
Route::get('/subjects/create', [SubjectController::class, 'create'])->name('subjects.create');

Route::post('/subjects', [SubjectController::class, 'store'])->name('subjects.store');

Route::get('display-subjects', function(){
    $datasets = [
        'andrus' => [
            'href' => 'https://hajus.ta23raamat.itmajakas.ee/api/movies',
            'custom_fields' => ['director', 'title', 'release_year'],
        ],
        'emotions' => [
            'href' => 'http://127.0.0.1:8000/subjects',
            'custom_fields' => ['title', 'category', 'emoji', 'intensity'],
        ]
    ];

    $data = match (request('type')) {
        'andrus' => Http::get($datasets['andrus']['href'])->json(),
        'emotions' => MyFavoriteSubject::all()->toArray(),
        default => MyFavoriteSubject::all()->toArray(),
    };

    $type = request('type', 'emotions'); // default to emotions
    $customFields = $datasets[$type]['custom_fields'] ?? ['title', 'description'];

    return Inertia::render('Subjects', [
        'data' => $data,
        'customFields' => $customFields,
        'currentType' => $type,
        'datasets' => array_keys($datasets)
    ]);
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
