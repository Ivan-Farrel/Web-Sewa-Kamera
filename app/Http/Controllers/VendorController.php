<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\KycSubmission;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class VendorController extends Controller
{
    public function index()
    {
        return $this->dashboard();
    }

    public function dashboard()
    {
        $user = Auth::user();

        // Izinkan role 'vendor', 'mitra', maupun 'admin'
        if (!in_array($user->role, ['vendor', 'mitra', 'admin'])) {
            abort(403, 'Akses khusus untuk Mitra / Penjual Kamera.');
        }

        // Ambil data KYC dengan aman
        $kyc = null;
        if (Schema::hasTable('kyc_submissions') && Schema::hasColumn('kyc_submissions', 'user_id')) {
            $kyc = KycSubmission::where('user_id', $user->id)->latest()->first();
        }

        // Kamera milik mitra (fallback ke produk contoh jika masih kosong)
        $myProducts = Schema::hasColumn('products', 'user_id')
            ? Product::where('user_id', $user->id)->latest()->get()
            : Product::latest()->take(6)->get();

        // Alias $products agar kompatibel di semua versi blade
        $products = $myProducts;

        // Pesanan sewa masuk
        $bookingQuery = Booking::query()->whereHas('product', fn ($query) => $query->where('user_id', $user->id));
        $bookings = Schema::hasTable('bookings') && Schema::hasColumn('bookings', 'product_id')
            ? (clone $bookingQuery)->latest()->get()
            : collect();

        // Perhitungan Saldo Rekber (Escrow) dengan pengecekan kolom aman
        $saldoTertahan = 0;
        $saldoSiapTarik = 0;
        $totalSelesai = 0;

        if (Schema::hasTable('bookings') && Schema::hasColumn('bookings', 'status')) {
            $rateColumn = Schema::hasColumn('bookings', 'daily_rate')
                ? 'daily_rate'
                : (Schema::hasColumn('bookings', 'total_price') ? 'total_price' : null);

            if ($rateColumn) {
                $saldoTertahan = (clone $bookingQuery)->where(function ($q) {
                    $q->where('status', 'like', '%Lunas%')
                      ->orWhere('status', 'like', '%verifikasi%')
                      ->orWhere('status', 'like', '%active%')
                      ->orWhere('status', 'like', '%pending%');
                })->sum('total');

                $saldoSiapTarik = (clone $bookingQuery)->where(function ($q) {
                    $q->where('status', 'like', '%Selesai%')
                      ->orWhere('status', 'like', '%completed%');
                })->sum('total');
            }

            $totalSelesai = (clone $bookingQuery)->where(function ($q) {
                $q->where('status', 'like', '%Selesai%')
                  ->orWhere('status', 'like', '%completed%');
            })->count();
        }

        return view('vendor.dashboard', compact(
            'user',
            'kyc',
            'myProducts',
            'products',
            'bookings',
            'saldoTertahan',
            'saldoSiapTarik',
            'totalSelesai'
        ));
    }

    public function storeProduct(Request $request)
    {
        abort_unless(in_array($request->user()->role, ['vendor', 'mitra'], true), 403);
        $data = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'category'          => ['required', 'string', 'max:50'],
            'price_8h'          => ['required', 'numeric', 'min:0'],
            'price_12h'         => ['required', 'numeric', 'min:0'],
            'price_24h'         => ['required', 'numeric', 'min:0'],
            'price_2d'          => ['required', 'numeric', 'min:0'],
            'overtime_per_hour' => ['required', 'numeric', 'min:0'],
            'image'             => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'image_file'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        // Dukung input bernama 'image' maupun 'image_file'
        $imageName = 'Canon 700D.jpg';
        $uploadedFile = $request->file('image') ?? $request->file('image_file');

        if ($uploadedFile) {
            $imageName = time() . '_' . Str::random(6) . '.' . $uploadedFile->getClientOriginalExtension();
            $uploadedFile->move(public_path('images'), $imageName);
        }

        $insertData = [
            'name'              => $data['name'],
            'category'          => $data['category'],
            'price_8h'          => $data['price_8h'],
            'price_12h'         => $data['price_12h'],
            'price_24h'         => $data['price_24h'],
            'price_2d'          => $data['price_2d'],
            'overtime_per_hour' => $data['overtime_per_hour'],
            'image'             => $imageName,
        ];

        if (Schema::hasColumn('products', 'user_id')) {
            $insertData['user_id'] = Auth::id();
        }

        if (Schema::hasColumn('products', 'status')) {
            $insertData['status'] = 'available';
        }

        Product::create($insertData);

        $msg = $request->listing_type === 'c2b'
            ? 'Pengajuan Titip Jual (C2B) berhasil dikirim ke Admin untuk inspeksi fisik & taksiran harga!'
            : 'Unit kamera berhasil ditambahkan ke katalog sewa & titip jual Anda!';

        return redirect()->route('vendor.dashboard')->with('success', $msg);
    }

    public function destroyProduct($id)
    {
        abort_unless(in_array(auth()->user()->role, ['vendor', 'mitra'], true), 403);
        $product = Product::where('user_id', auth()->id())->findOrFail($id);
        $product->delete();

        return redirect()->route('vendor.dashboard')->with('success', 'Unit produk berhasil dihapus dari etalase.');
    }

    public function confirmBooking($id)
    {
        abort_unless(in_array(auth()->user()->role, ['vendor', 'mitra'], true), 403);
        $booking = Booking::whereHas('product', fn ($query) => $query->where('user_id', auth()->id()))->findOrFail($id);
        abort_unless(str_contains($booking->status, 'Lunas'), 409, 'Booking belum lunas.');
        if (Schema::hasColumn('bookings', 'status')) {
            $booking->status = 'Disewa (Aktif)';
            $booking->save();
        }

        return redirect()->route('vendor.dashboard')->with('success', 'Pengambilan unit dikonfirmasi! Status unit kini DISEWA.');
    }

    public function confirmReturn(Request $request, $id)
    {
        abort_unless(in_array($request->user()->role, ['vendor', 'mitra'], true), 403);
        $data = $request->validate(['late_days' => ['nullable', 'integer', 'min:0', 'max:365'], 'damage_fee' => ['nullable', 'integer', 'min:0']]);
        $booking = Booking::whereHas('product', fn ($query) => $query->where('user_id', $request->user()->id))->findOrFail($id);
        abort_unless(str_contains($booking->status, 'Aktif'), 409, 'Booking belum berstatus aktif.');
        $lateDays = (int) ($data['late_days'] ?? 0);
        $damageFee = (int) ($data['damage_fee'] ?? 0);

        $product = Product::find($booking->product_id ?? null);
        $dailyRate = $product ? $product->price_24h : ($booking->daily_rate ?? 100000);

        // Rumus Denda Telat 150% x Tarif Harian x Hari Telat
        $lateFine = $lateDays > 0 ? ($dailyRate * 1.5 * $lateDays) : 0;
        $totalFine = $lateFine + $damageFee;

        if (Schema::hasColumn('bookings', 'status')) {
            $booking->status = 'Selesai';
            $booking->save();
        }

        if ($totalFine > 0) {
            return redirect()->route('vendor.dashboard')->with(
                'success',
                'Pengembalian dicatat! Tagihan denda otomatis (150%): Rp ' . number_format($totalFine, 0, ',', '.') . '. Dana Rekber siap dicairkan.'
            );
        }

        return redirect()->route('vendor.dashboard')->with(
            'success',
            'Unit kembali tepat waktu dalam kondisi baik! Dana Rekening Bersama (Escrow) siap ditarik.'
        );
    }

    public function uploadKyc(Request $request)
    {
        abort_unless(in_array($request->user()->role, ['vendor', 'mitra'], true), 403);
        $request->validate([
            'ktp_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $file = $request->file('ktp_image');
        $filename = 'ktp_mitra_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/guarantees'), $filename);

        if (Schema::hasTable('kyc_submissions')) {
            $kycData = [];
            
            if (Schema::hasColumn('kyc_submissions', 'user_id')) {
                $kycData['user_id'] = Auth::id();
            }
            if (Schema::hasColumn('kyc_submissions', 'nama_lengkap')) {
                $kycData['nama_lengkap'] = Auth::user()->name;
            }
            if (Schema::hasColumn('kyc_submissions', 'nik')) {
                $kycData['nik'] = $request->input('nik', '0000000000000000');
            }
            if (Schema::hasColumn('kyc_submissions', 'tanggal_lahir')) {
                $kycData['tanggal_lahir'] = $request->input('tanggal_lahir', '2003-12-07');
            }
            if (Schema::hasColumn('kyc_submissions', 'tempat_lahir')) {
                $kycData['tempat_lahir'] = $request->input('tempat_lahir', 'Papar, Kediri');
            }
            if (Schema::hasColumn('kyc_submissions', 'alamat')) {
                $kycData['alamat'] = $request->input('alamat', 'Papar, Kediri, Jawa Timur');
            }
            if (Schema::hasColumn('kyc_submissions', 'jenis_kelamin')) {
                $kycData['jenis_kelamin'] = $request->input('jenis_kelamin', 'Laki-laki');
            }
            
            // Simpan ke ktp_path jika ada
            if (Schema::hasColumn('kyc_submissions', 'ktp_path')) {
                $kycData['ktp_path'] = 'uploads/guarantees/' . $filename;
            }
            // Simpan ke dokumen_path jika ada (Ini yang nyelesain error lu sekarang)
            if (Schema::hasColumn('kyc_submissions', 'dokumen_path')) {
                $kycData['dokumen_path'] = 'uploads/guarantees/' . $filename;
            }
            
            if (Schema::hasColumn('kyc_submissions', 'status')) {
                $kycData['status'] = 'pending';
            }
            
            if (!empty($kycData)) {
                \App\Models\KycSubmission::create($kycData);
            }
        }

        return redirect()->route('vendor.dashboard')->with('success', 'Dokumen KYC (KTP/KTM) berhasil diunggah dan sedang diverifikasi Admin.');
    }

    public function withdraw(Request $request)
    {
        abort_unless(in_array($request->user()->role, ['vendor', 'mitra'], true), 403);
        $request->validate([
            'bank_name'      => ['required', 'string'],
            'account_number' => ['required', 'string'],
            'amount'         => ['required', 'numeric', 'min:50000'],
        ]);

        return redirect()->route('vendor.dashboard')->with(
            'success',
            'Penarikan saldo Rekber sebesar Rp ' . number_format($request->amount, 0, ',', '.') . ' berhasil diproses ke rekening ' . $request->bank_name . ' Anda.'
        );
    }
}
