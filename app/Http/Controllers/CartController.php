<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * Display the cart.
     */
    public function index()
    {
        $cart = session('cart', []);
        $total = collect($cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        return Inertia::render('Cart/Index', [
            'cartItems' => $cart,
            'total' => $total,
            'itemCount' => collect($cart)->sum('quantity'),
        ]);
    }

    /**
     * Add a product to the cart.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::find($request->product_id);
        $cart = session('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $request->quantity;
        } else {
            $cart[$product->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image,
                'quantity' => $request->quantity,
            ];
        }

        session(['cart' => $cart]);
        Session::save();

        return redirect()->back()->with('success', 'Product added to cart!');
    }

    /**
     * Update the quantity of a cart item.
     */
    public function update(Request $request, $productId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = session('cart', []);
        
        // Try both string and numeric keys
        $key = null;
        if (isset($cart[$productId])) {
            $key = $productId;
        } elseif (isset($cart[(string)$productId])) {
            $key = (string)$productId;
        }
        
        if ($key !== null) {
            $cart[$key]['quantity'] = $request->quantity;
            session(['cart' => $cart]);
            Session::save();
            return redirect()->route('cart.index')->with('success', 'Cart updated!');
        }

        return redirect()->route('cart.index')->with('error', 'Product not found in cart!');
    }

    /**
     * Remove a product from the cart.
     */
    public function destroy($productId)
    {
        $cart = session('cart', []);
        
        // Try both string and numeric keys
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session(['cart' => $cart]);
        } elseif (isset($cart[(string)$productId])) {
            unset($cart[(string)$productId]);
            session(['cart' => $cart]);
        }
        
        Session::save();

        return redirect()->route('cart.index')->with('success', 'Product removed from cart!');
    }

    /**
     * Clear the entire cart.
     */
    public function clear()
    {
        session()->forget('cart');
        Session::save();
        
        return redirect()->route('cart.index')->with('success', 'Cart cleared!');
    }
}
