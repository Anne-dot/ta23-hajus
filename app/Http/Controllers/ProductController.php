<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        // return dd(Product::all());
        return Inertia::render('product/Index',[
            'products' => Product::all(),
            'cartItems' => session('cart', [])
        ]);
    }
}
