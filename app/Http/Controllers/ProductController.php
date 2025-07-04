<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        return Inertia::render('product/Index', [
            'products' => Product::all(),
            'cartItems' => session('cart', []),
        ]);
    }
}
