<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

// Monolith style: Controller khud validation + DB query + response
// sab kuch handle kar raha hai. Koi Service/Repository layer nahi.
class ProductController extends Controller
{
    public function index()
    {
        $products = Product::where('is_active', true)->paginate(20);

        return view('products.index', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        $product = Product::create($validated);

        return redirect()->route('products.show', $product)
            ->with('success', 'Product created.');
    }
}
