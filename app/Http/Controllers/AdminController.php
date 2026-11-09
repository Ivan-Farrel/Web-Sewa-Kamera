<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\KycSubmission;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        if ($user->role !== 'admin') {
            abort(403, 'Akses terbatas untuk Administrator.');
        }

        $totalUsers = User::count();
        $totalMitra = User::where('role', 'mitra')->count();
        $totalCustomer = User::where('role', 'customer')->count();

        $bookings = Booking::latest()->get();
        $totalBookings = $bookings->count();
        $totalEscrow = $bookings->sum('total');

        $kycSubmissions = KycSubmission::with('user')->latest()->get();
        $totalKyc = $kycSubmissions->count();

        $products = Product::latest()->take(10)->get();
        $totalProducts = Product::count();

        $users = User::latest()->get();

        return view('admin.dashboard', compact(
            'user',
            'totalUsers',
            'totalMitra',
            'totalCustomer',
            'bookings',
            'totalBookings',
            'totalEscrow',
            'kycSubmissions',
            'totalKyc',
            'products',
            'totalProducts',
            'users'
        ));
    }

    public function updateKycStatus(Request $request, $id)
    {
        $kyc = KycSubmission::findOrFail($id);
        $status = $request->input('status', 'Disetujui');
        $kyc->update(['status' => $status]);

        return back()->with('success', "Status KYC milik {$kyc->nama_lengkap} berhasil diubah menjadi: {$status}.");
    }

    public function updateBookingStatus(Request $request, $code)
    {
        $booking = Booking::where('booking_code', $code)->firstOrFail();
        $status = $request->input('status');
        $booking->update(['status' => $status]);

        return back()->with('success', "Status pesanan #{$code} berhasil diperbarui.");
    }
}
