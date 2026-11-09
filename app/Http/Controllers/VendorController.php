<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VendorController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        if ($user->role !== 'mitra' && $user->role !== 'admin') {
            abort(403, 'Akses khusus untuk Mitra / Penjual Kamera.');
        }

        $kyc = $user->kycSubmissions()->latest()->first();

        // Kamera milik mitra
        $myProducts = Product::where('user_id', $user->id)->latest()->get();
        if ($myProducts->isEmpty()) {
            $myProducts = Product::whereIn('id', [1, 4, 8])->get();
        }

        // Pesanan sewa masuk
        $bookings = Booking::latest()->get();

        // Perhitungan Saldo Rekber
        $saldoTertahan = Booking::where(function ($q) {
            $q->where('status', 'like', '%Lunas%')
              ->orWhere('status', 'like', '%verifikasi%');
        })->sum('daily_rate');

        $saldoSiapTarik = Booking::where('status', 'like', '%Selesai%')->sum('daily_rate');
        $totalSelesai = Booking::where('status', 'like', '%Selesai%')->count();

        return view('vendor.dashboard', compact(
            'user',
            'kyc',
            'myProducts',
            'bookings',
            'saldoTertahan',
            'saldoSiapTarik',
            'totalSelesai'
        ));
    }

    public function storeProduct(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:50'],
            'price_8h' => ['required', 'integer', 'min:10000'],
            'price_12h' => ['required', 'integer', 'min:10000'],
            'price_24h' => ['required', 'integer', 'min:10000'],
            'price_2d' => ['required', 'integer', 'min:10000'],
            'overtime_per_hour' => ['required', 'integer', 'min:1000'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        $imageName = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageName = time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $imageName);
        }

        Product::create([
            'user_id' => auth()->id(),
            'name' => $data['name'],
            'category' => $data['category'],
            'price_8h' => $data['price_8h'],
            'price_12h' => $data['price_12h'],
            'price_24h' => $data['price_24h'],
            'price_2d' => $data['price_2d'],
            'overtime_per_hour' => $data['overtime_per_hour'],
            'status' => 'available',
            'image' => $imageName,
        ]);

        return redirect()->route('vendor.dashboard')->with('success', 'Unit kamera berhasil ditambahkan ke katalog sewa & titip jual Anda!');
    }

    public function withdraw(Request $request)
    {
        $request->validate([
            'bank_name' => ['required', 'string'],
            'account_number' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:50000'],
        ]);

        return redirect()->route('vendor.dashboard')->with('success', 'Penarikan saldo sebesar Rp ' . number_format($request->amount, 0, ',', '.') . ' berhasil diproses ke rekening Anda.');
    }
}
