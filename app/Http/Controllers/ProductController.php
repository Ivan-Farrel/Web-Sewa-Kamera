<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        // Ambil semua produk dari database
        $products = Product::all();

        // Lempar data ke view blade (sesuaikan nama filenya, misal 'home' atau 'dashboard')
        return view('home', compact('products'));
    }
}