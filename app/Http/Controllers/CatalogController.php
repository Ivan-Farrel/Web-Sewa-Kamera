<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $products = [
            ['id' => 'sony-a7-iii', 'name' => 'Sony A7 III', 'price' => 250000, 'rating' => '4.8 (120)', 'category' => 'Kamera Body'],
            ['id' => 'canon-eos-r50', 'name' => 'Canon EOS R50', 'price' => 180000, 'rating' => '4.5 (120)', 'category' => 'Kamera Body'],
            ['id' => 'fujifilm-x-t30', 'name' => 'Fujifilm X-T30', 'price' => 200000, 'rating' => '4.8 (120)', 'category' => 'Kamera Body'],
            ['id' => 'sony-alpha-6400', 'name' => 'Sony Alpha 6400', 'price' => 170000, 'rating' => '4.6 (120)', 'category' => 'Kamera Body'],
            ['id' => 'canon-80d', 'name' => 'Canon 80D', 'price' => 250000, 'rating' => '4.5 (120)', 'category' => 'Kamera Body'],
            ['id' => 'nikon-d750', 'name' => 'Nikon D750', 'price' => 250000, 'rating' => '4.7 (120)', 'category' => 'Kamera Body'],
        ];

        $filtered = collect($products)->filter(function ($product) use ($request) {
            return str_contains(mb_strtolower($product['name']), mb_strtolower($request->string('q')->toString()))
                && (! $request->filled('kategori') || $product['category'] === $request->input('kategori'))
                && (! $request->filled('harga_min') || $product['price'] >= (int) $request->input('harga_min'))
                && (! $request->filled('harga_max') || $product['price'] <= (int) $request->input('harga_max'));
        })->values();

        return response()->json(['data' => $filtered]);
    }
}
