<?php

use App\Http\Controllers\CommetnController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MarkerController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SubjectController;
use App\Models\Commetn;
use App\Models\Marker;
use App\Models\MyFavoriteSubject;
use Illuminate\Http\Request;
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
        ],
        'henrik' => [
            'href' => 'https://hajus.ta23mutt.itmajakas.ee/fight-cards/07b4cc44-9042-4944-b1dc-56eac757ca4f',
            'custom_fields' => ['title', 'description'],
        ],
    ];

    $data = match (request('type')) {
        'andrus' => Http::get($datasets['andrus']['href'])->json(),
        'emotions' => MyFavoriteSubject::all()->toArray(),
        'henrik' => Http::get($datasets['henrik']['href'])->json(),
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

Route::get('/products', [ProductController::class, 'index'])->name('products.index');

// Cart routes - accessible to everyone (guests and authenticated users)
Route::controller(\App\Http\Controllers\CartController::class)->prefix('cart')->name('cart.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/', 'store')->name('store');
    Route::delete('/clear', 'clear')->name('clear'); // Move this before the parameterized route
    Route::patch('/{product}', 'update')->name('update');
    Route::delete('/{product}', 'destroy')->name('destroy');
});

// Checkout routes
Route::post('/checkout', function (Request $request) {
    try {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        
        $cart = session('cart', []);
        $total = collect($cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
        
        if ($total <= 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }
        
        $session = \Stripe\Checkout\Session::create([
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => ['name' => 'Order Total'],
                    'unit_amount' => $total * 100, // Convert to cents
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => url('/checkout/success'),
            'cancel_url' => url('/cart'),
        ]);
        
        return redirect($session->url);
    } catch (\Exception $e) {
        dd([
            'error' => $e->getMessage(),
            'stripe_key_exists' => !empty(env('STRIPE_SECRET')),
            'total' => $total ?? 0,
            'cart' => $cart ?? []
        ]);
    }
})->name('checkout');

Route::get('/checkout/success', function () {
    session()->forget('cart');
    return Inertia::render('Checkout/Success');
})->name('checkout.success');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
