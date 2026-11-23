<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\KycSubmission;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        if (!$user || $user->role !== 'admin') {
            abort(403, 'Akses terbatas untuk Administrator.');
        }

        $totalUsers = User::count();
        $totalMitra = User::whereIn('role', ['mitra', 'vendor'])->count();
        $totalCustomer = User::where('role', 'customer')->count();

        $bookings = Booking::latest()->get();
        $totalBookings = $bookings->count();

        // Hitung total escrow dengan pengecekan kolom yang aman
        if (Schema::hasColumn('bookings', 'total')) {
            $totalEscrow = $bookings->sum('total');
        } elseif (Schema::hasColumn('bookings', 'total_price')) {
            $totalEscrow = $bookings->sum('total_price');
        } elseif (Schema::hasColumn('bookings', 'daily_rate')) {
            $totalEscrow = $bookings->sum('daily_rate');
        } else {
            $totalEscrow = 0;
        }

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
        abort_unless($request->user() && $request->user()->role === 'admin', 403, 'Akses terbatas untuk Administrator.');

        $request->validate([
            'status' => ['required', 'string'],
        ]);

        $kyc = KycSubmission::findOrFail($id);

        // Terima input tombol versi Indonesia maupun Inggris lalu seragamkan
        $rawStatus = strtolower(trim($request->input('status')));

        $statusMap = [
            'disetujui'       => 'Disetujui',
            'setujui'         => 'Disetujui',
            'approved'        => 'Disetujui',
            'approve'         => 'Disetujui',
            'verified'        => 'Disetujui',
            'ditolak'         => 'Ditolak',
            'tolak'           => 'Ditolak',
            'rejected'        => 'Ditolak',
            'reject'          => 'Ditolak',
            'menunggu review' => 'Menunggu Review',
            'pending'         => 'Menunggu Review',
        ];

        if (!isset($statusMap[$rawStatus])) {
            return back()->withErrors(['status' => 'Status KYC tidak valid.']);
        }

        $finalStatus = $statusMap[$rawStatus];

        // Assign langsung + save() agar tidak terblokir $fillable di Model
        if (Schema::hasColumn('kyc_submissions', 'status')) {
            $kyc->status = $finalStatus;
            $kyc->save();
        }

        // Sinkronkan juga ke tabel users jika kolom status KYC tersedia
        if (!empty($kyc->user_id)) {
            $mitra = User::find($kyc->user_id);
            if ($mitra) {
                $updatedUser = false;
                if (Schema::hasColumn('users', 'status_kyc')) {
                    $mitra->status_kyc = $finalStatus;
                    $updatedUser = true;
                }
                if (Schema::hasColumn('users', 'kyc_status')) {
                    $mitra->kyc_status = $finalStatus;
                    $updatedUser = true;
                }
                if ($updatedUser) {
                    $mitra->save();
                }
            }
        }

        $nama = $kyc->nama_lengkap ?? ($kyc->user->name ?? 'Mitra');

        return back()->with('success', "Status KYC milik {$nama} berhasil diubah menjadi: {$finalStatus}.");
    }

    public function updateBookingStatus(Request $request, $code)
    {
        abort_unless($request->user() && $request->user()->role === 'admin', 403, 'Akses terbatas untuk Administrator.');

        $request->validate([
            'status' => ['required', 'in:Menunggu verifikasi jaminan,Lunas (Siap Diambil / Dikirim),Disewa (Aktif),Selesai,Dibatalkan'],
        ]);

        $booking = Schema::hasColumn('bookings', 'booking_code')
            ? Booking::where('booking_code', $code)->firstOrFail()
            : Booking::findOrFail($code);

        $status = $request->input('status');

        // Assign langsung + save() agar aman dari masalah $fillable
        if (Schema::hasColumn('bookings', 'status')) {
            $booking->status = $status;
            $booking->save();
        }

        return back()->with('success', "Status pesanan #{$code} berhasil diperbarui menjadi: {$status}.");
    }
}