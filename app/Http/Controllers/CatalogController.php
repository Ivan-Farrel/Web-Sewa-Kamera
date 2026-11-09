<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index()
    {
        $products = Product::all();
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