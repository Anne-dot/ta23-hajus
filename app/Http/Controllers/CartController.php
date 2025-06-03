<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

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
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::find($request->product_id);
        $cart = session('cart', []);

        $currentQuantityInCart = isset($cart[$product->id]) ? $cart[$product->id]['quantity'] : 0;
        $totalQuantity = $currentQuantityInCart + $request->quantity;

        if ($totalQuantity > $product->quantity) {
            $availableToAdd = $product->quantity - $currentQuantityInCart;
            if ($availableToAdd <= 0) {
                return redirect()->back()->with('error', 'Cannot add more items. You already have the maximum available stock in your cart.');
            }

            return redirect()->back()->with('error', "Only {$availableToAdd} more items available. Current stock: {$product->quantity}");
        }

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
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session('cart', []);
        $product = Product::find($productId);

        if (! $product) {
            return redirect()->route('cart.index')->with('error', 'Product not found!');
        }

        if ($request->quantity > $product->quantity) {
            return redirect()->route('cart.index')->with('error', "Only {$product->quantity} items available in stock!");
        }

        $key = null;
        if (isset($cart[$productId])) {
            $key = $productId;
        } elseif (isset($cart[(string) $productId])) {
            $key = (string) $productId;
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

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session(['cart' => $cart]);
        } elseif (isset($cart[(string) $productId])) {
            unset($cart[(string) $productId]);
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
