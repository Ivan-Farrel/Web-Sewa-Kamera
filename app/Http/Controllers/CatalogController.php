<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = Product::query()->where('status', 'available');
        if ($request->filled('q')) {
            $term = trim($request->string('q')->toString());
            $query->where(fn ($builder) => $builder->where('name', 'like', "%{$term}%")
                ->orWhere('category', 'like', "%{$term}%"));
        }
        $products = $query->latest()->get();
        return view('products.index', compact('products'));
    }

    public function show($id = null)
    {
        if ($id === 'detail' || !$id) {
            $product = Product::where('name', 'like', '%Sony A7 III%')->first() ?? Product::first();
        } else {
            $product = Product::find($id) ?? Product::first();
        }

        if (!$product) {
            abort(404, 'Produk tidak ditemukan');
        }

        return view('products.show', compact('product'));
    }
}
