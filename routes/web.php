<?php

use App\Http\Controllers\CommetnController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MarkerController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SubjectController;
use App\Models\MyFavoriteSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
    'comments' => 'commetn',
]);

Route::get('/subjects', [SubjectController::class, 'index']);
Route::get('/subjects/create', [SubjectController::class, 'create'])->name('subjects.create');
Route::post('/subjects', [SubjectController::class, 'store'])->name('subjects.store');

Route::get('/display-subjects', function () {
    $datasets = [
        'andrus' => [
            'href' => 'https://hajus.ta23raamat.itmajakas.ee/api/movies',
            'custom_fields' => ['director', 'title', 'release_year'],
        ],
        'emotions' => [
            'href' => url('/subjects'),
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
        'datasets' => array_keys($datasets),
    ]);
});

Route::get('/products', [ProductController::class, 'index'])->name('products.index');

Route::controller(\App\Http\Controllers\CartController::class)->prefix('cart')->name('cart.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/', 'store')->name('store');
    Route::delete('/clear', 'clear')->name('clear');
    Route::patch('/{product}', 'update')->name('update');
    Route::delete('/{product}', 'destroy')->name('destroy');
});

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
                    'currency' => 'eur',
                    'product_data' => ['name' => 'Order Total'],
                    'unit_amount' => $total * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('checkout.confirm'),
            'cancel_url' => route('cart.index'),
            'metadata' => [
                'user_id' => auth()->id() ?? 'guest',
            ],
        ]);

        // Create order immediately with pending status
        $order = \App\Models\Order::create([
            'user_id' => auth()->id(),
            'stripe_session_id' => $session->id,
            'status' => 'pending',
            'total_amount' => $total,
        ]);

        foreach ($cart as $item) {
            \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['id'],
                'product_name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'total' => $item['price'] * $item['quantity'],
            ]);
        }

        return redirect()->away($session->url);
    } catch (\Exception $e) {
        \Log::error('Checkout error: '.$e->getMessage());

        return redirect()->route('cart.index')->with('error', 'Unable to process payment. Please try again later.');
    }
})->name('checkout');

Route::get('/checkout/confirm', function () {
    try {
        if (! auth()->check()) {
            return redirect()->route('cart.index')->with('error', 'Session expired. Please try checkout again');
        }

        $order = \App\Models\Order::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->where('created_at', '>', now()->subMinutes(10))
            ->latest()
            ->first();

        if (! $order) {
            return redirect()->route('cart.index')->with('error', 'No recent order found');
        }

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $stripeSession = \Stripe\Checkout\Session::retrieve($order->stripe_session_id);

        if ($stripeSession->payment_status !== 'paid') {
            return redirect()->route('cart.index')->with('error', 'Payment not completed');
        }

        $order->update([
            'status' => 'completed',
            'customer_email' => $stripeSession->customer_details->email,
            'customer_name' => $stripeSession->customer_details->name,
            'paid_at' => now(),
        ]);

        foreach ($order->items as $item) {
            $product = \App\Models\Product::find($item->product_id);
            if ($product) {
                $product->decrement('quantity', $item->quantity);
            }
        }

        session()->forget('cart');

        return redirect()->route('checkout.success', ['order_id' => $order->id]);
    } catch (\Exception $e) {
        \Log::error('Checkout confirm error: '.$e->getMessage());

        return redirect()->route('cart.index')->with('error', 'Unable to confirm payment');
    }
})->name('checkout.confirm');

Route::get('/checkout/success', function (Request $request) {
    $orderId = $request->get('order_id');

    if (! $orderId) {
        return redirect()->route('products.index');
    }

    $order = \App\Models\Order::with('items')->find($orderId);

    if (! $order || ($order->user_id && $order->user_id !== auth()->id())) {
        return redirect()->route('products.index');
    }

    return Inertia::render('Checkout/Success', [
        'order' => $order,
    ]);
})->name('checkout.success');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
