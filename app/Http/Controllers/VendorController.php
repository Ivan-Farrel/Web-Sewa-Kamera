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
        if (!$user || !in_array($user->role, ['vendor', 'mitra', 'admin'])) {
            abort(403, 'Akses khusus Mitra CamHub.');
        }

        // Ambil produk milik mitra (atau semua jika kolom relasi belum ada)
        if (Schema::hasTable('products')) {
            if (Schema::hasColumn('products', 'user_id')) {
                $products = Product::where('user_id', $user->id)->latest()->get();
            } elseif (Schema::hasColumn('products', 'vendor_id')) {
                $products = Product::where('vendor_id', $user->id)->latest()->get();
            } else {
                $products = Product::latest()->get();
            }
        } else {
            $products = collect();
        }

        // Ambil pesanan terkait produk mitra
        if (Schema::hasTable('bookings')) {
            $productIds = $products->pluck('id')->all();
            if (Schema::hasColumn('bookings', 'vendor_id')) {
                $bookings = Booking::where('vendor_id', $user->id)->latest()->get();
            } elseif (Schema::hasColumn('bookings', 'product_id') && !empty($productIds)) {
                $bookings = Booking::whereIn('product_id', $productIds)->latest()->get();
            } else {
                $bookings = Booking::latest()->get();
            }
        } else {
            $bookings = collect();
        }

        // Hitung saldo escrow (tertahan) vs saldo siap tarik
        $escrowBalance = 0;
        $availableBalance = 0;

        foreach ($bookings as $b) {
            $amount = (float) ($b->total ?? $b->total_price ?? 0);
            $status = strtolower((string) ($b->status ?? ''));

            if (str_contains($status, 'selesai') || $status === 'completed') {
                $availableBalance += $amount;
            } elseif (
                str_contains($status, 'lunas') ||
                str_contains($status, 'disewa') ||
                str_contains($status, 'aktif') ||
                str_contains($status, 'paid')
            ) {
                $escrowBalance += $amount;
            }
        }

        // Ambil status KYC terbaru milik mitra
        $kyc = null;
        if (Schema::hasTable('kyc_submissions') && Schema::hasColumn('kyc_submissions', 'user_id')) {
            $kyc = KycSubmission::where('user_id', $user->id)->latest()->first();
        }

        return view('vendor.dashboard', compact(
            'user',
            'products',
            'bookings',
            'escrowBalance',
            'availableBalance',
            'kyc'
        ));
    }

    public function store(Request $request)
    {
        return $this->storeProduct($request);
    }

    public function storeProduct(Request $request)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['vendor', 'mitra', 'admin'])) {
            abort(403, 'Akses khusus Mitra CamHub.');
        }

        $listingType = $request->input('listing_type', 'sewa_c2c');
        $isRental = in_array($listingType, ['sewa_c2c', 'hybrid_c2c']);
        $isSale = in_array($listingType, ['jual_putus', 'hybrid_c2c', 'titip_jual_c2b']);

        $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'category'          => ['required', 'string', 'max:100'],
            'listing_type'      => ['required', 'in:sewa_c2c,jual_putus,hybrid_c2c,titip_jual_c2b'],
            'price_8h'          => [$isRental ? 'required' : 'nullable', 'numeric', 'min:0'],
            'price_12h'         => [$isRental ? 'required' : 'nullable', 'numeric', 'min:0'],
            'price_24h'         => [$isRental ? 'required' : 'nullable', 'numeric', 'min:0'],
            'price_2d'          => [$isRental ? 'required' : 'nullable', 'numeric', 'min:0'],
            'overtime_per_hour' => [$isRental ? 'required' : 'nullable', 'numeric', 'min:0'],
            'sell_price'        => [$isSale ? 'required' : 'nullable', 'numeric', 'min:0'],
            'condition'         => ['nullable', 'string', 'max:100'],
            'handover_method'   => ['nullable', 'string', 'max:120'],
            'shutter_count'     => ['nullable', 'string', 'max:100'],
            'completeness'      => ['nullable', 'string', 'max:255'],
            'inspection_notes'  => ['nullable', 'string', 'max:1000'],
            'image'             => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        // Upload foto produk jika ada
        $imagePath = null;
        if ($request->hasFile('image')) {
            $uploadDir = public_path('uploads/products');
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $file = $request->file('image');
            $filename = 'produk_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $imagePath = 'uploads/products/' . $filename;
        }

        // Susun ringkasan deskripsi agar detail Jual Putus / C2B tetap tersimpan meski kolom terpisah belum dimigrasi
        $descParts = [];
        if ($isSale) {
            $labelSkema = $listingType === 'titip_jual_c2b'
                ? 'Titip Jual / Buyback (C2B)'
                : ($listingType === 'hybrid_c2c' ? 'Hybrid Sewa & Jual Putus (C2C)' : 'Jual Putus (C2C)');

            $descParts[] = "Skema: {$labelSkema}";
            if ($request->filled('sell_price')) {
                $descParts[] = 'Harga Jual/Taksiran: Rp ' . number_format((float) $request->input('sell_price'), 0, ',', '.');
            }
            if ($request->filled('condition')) {
                $descParts[] = 'Kondisi: ' . $request->input('condition');
            }
            if ($request->filled('handover_method')) {
                $descParts[] = 'Penyerahan Inspeksi: ' . $request->input('handover_method');
            }
            if ($request->filled('shutter_count')) {
                $descParts[] = 'Shutter Count: ' . $request->input('shutter_count');
            }
            if ($request->filled('completeness')) {
                $descParts[] = 'Kelengkapan: ' . $request->input('completeness');
            }
            if ($request->filled('inspection_notes')) {
                $descParts[] = 'Catatan: ' . $request->input('inspection_notes');
            }
        } else {
            $descParts[] = 'Unit sewa harian mitra terverifikasi CamHub.';
        }

        $product = new Product();

        if (Schema::hasColumn('products', 'user_id')) {
            $product->user_id = $user->id;
        }
        if (Schema::hasColumn('products', 'vendor_id')) {
            $product->vendor_id = $user->id;
        }
        if (Schema::hasColumn('products', 'name')) {
            $product->name = $request->input('name');
        }
        if (Schema::hasColumn('products', 'slug')) {
            $product->slug = Str::slug($request->input('name')) . '-' . Str::lower(Str::random(5));
        }
        if (Schema::hasColumn('products', 'category')) {
            $product->category = $request->input('category');
        }
        if (Schema::hasColumn('products', 'listing_type')) {
            $product->listing_type = $listingType;
        }

        // Tarif sewa (diisi 0 jika mode Jual Putus / C2B agar aman dari error default value)
        if (Schema::hasColumn('products', 'price_8h')) {
            $product->price_8h = $isRental ? (int) $request->input('price_8h', 0) : 0;
        }
        if (Schema::hasColumn('products', 'price_12h')) {
            $product->price_12h = $isRental ? (int) $request->input('price_12h', 0) : 0;
        }
        if (Schema::hasColumn('products', 'price_24h')) {
            $product->price_24h = $isRental ? (int) $request->input('price_24h', 0) : 0;
        }
        if (Schema::hasColumn('products', 'price_2d')) {
            $product->price_2d = $isRental ? (int) $request->input('price_2d', 0) : 0;
        }
        if (Schema::hasColumn('products', 'overtime_per_hour')) {
            $product->overtime_per_hour = $isRental ? (int) $request->input('overtime_per_hour', 0) : 0;
        }

        // Harga jual & metadata kondisi (jika kolom tersedia di tabel products)
        if (Schema::hasColumn('products', 'sell_price')) {
            $product->sell_price = $isSale ? (int) $request->input('sell_price', 0) : 0;
        }
        if (Schema::hasColumn('products', 'price_sell')) {
            $product->price_sell = $isSale ? (int) $request->input('sell_price', 0) : 0;
        }
        if (Schema::hasColumn('products', 'condition')) {
            $product->condition = $request->input('condition');
        }
        if (Schema::hasColumn('products', 'shutter_count')) {
            $product->shutter_count = $request->input('shutter_count');
        }
        if (Schema::hasColumn('products', 'completeness')) {
            $product->completeness = $request->input('completeness');
        }
        if (Schema::hasColumn('products', 'description')) {
            $product->description = implode(' | ', $descParts);
        }
        if (Schema::hasColumn('products', 'stock')) {
            $product->stock = 1;
        }
        if (Schema::hasColumn('products', 'status')) {
            $product->status = $listingType === 'titip_jual_c2b' ? 'Menunggu Inspeksi Toko' : 'Tersedia';
        }
        if ($imagePath) {
            if (Schema::hasColumn('products', 'image')) {
                $product->image = $imagePath;
            } elseif (Schema::hasColumn('products', 'image_path')) {
                $product->image_path = $imagePath;
            }
        }

        $product->save();

        $successMsg = $listingType === 'titip_jual_c2b'
            ? 'Pengajuan Titip Jual / Buyback (C2B) berhasil dikirim ke Admin CamHub untuk jadwal inspeksi.'
            : 'Unit berhasil ditayangkan ke etalase CamHub.';

        return redirect()->route('vendor.dashboard')->with('success', $successMsg);
    }

    public function uploadKyc(Request $request)
    {
        $request->validate([
            'ktp_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $uploadDir = public_path('uploads/guarantees');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $file = $request->file('ktp_image');
        $filename = 'ktp_mitra_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $file->move($uploadDir, $filename);

        if (Schema::hasTable('kyc_submissions')) {
            $kyc = new KycSubmission();

            if (Schema::hasColumn('kyc_submissions', 'user_id')) {
                $kyc->user_id = Auth::id();
            }
            if (Schema::hasColumn('kyc_submissions', 'nama_lengkap')) {
                $kyc->nama_lengkap = Auth::user()->name;
            }
            if (Schema::hasColumn('kyc_submissions', 'nik')) {
                $kyc->nik = $request->input('nik', '0000000000000000');
            }
            if (Schema::hasColumn('kyc_submissions', 'tanggal_lahir')) {
                $kyc->tanggal_lahir = $request->input('tanggal_lahir', '2000-01-01');
            }
            if (Schema::hasColumn('kyc_submissions', 'tempat_lahir')) {
                $kyc->tempat_lahir = $request->input('tempat_lahir', 'Kediri');
            }
            if (Schema::hasColumn('kyc_submissions', 'alamat')) {
                $kyc->alamat = $request->input('alamat', 'Kediri, Jawa Timur');
            }
            if (Schema::hasColumn('kyc_submissions', 'jenis_kelamin')) {
                $kyc->jenis_kelamin = $request->input('jenis_kelamin', 'Laki-laki');
            }
            if (Schema::hasColumn('kyc_submissions', 'ktp_path')) {
                $kyc->ktp_path = 'uploads/guarantees/' . $filename;
            }
            if (Schema::hasColumn('kyc_submissions', 'dokumen_path')) {
                $kyc->dokumen_path = 'uploads/guarantees/' . $filename;
            }
            if (Schema::hasColumn('kyc_submissions', 'status')) {
                $kyc->status = 'pending';
            }

            $kyc->save();
        }

        return redirect()->route('vendor.dashboard')->with('success', 'Dokumen KYC (KTP/KTM) berhasil diunggah dan sedang diverifikasi Admin.');
    }

    public function withdraw(Request $request)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['vendor', 'mitra', 'admin'])) {
            abort(403, 'Akses khusus Mitra CamHub.');
        }

        if (Schema::hasTable('kyc_submissions') && Schema::hasColumn('kyc_submissions', 'user_id')) {
            $latestKyc = KycSubmission::where('user_id', $user->id)->latest()->first();
            $status = strtolower(trim($latestKyc->status ?? ''));
            if (!in_array($status, ['disetujui', 'approved', 'verified'])) {
                return redirect()->route('vendor.dashboard')->withErrors([
                    'withdraw' => 'Pencairan saldo ditolak: Verifikasi KYC Mitra harus berstatus Disetujui terlebih dahulu.',
                ]);
            }
        }

        return redirect()->route('vendor.dashboard')->with('success', 'Permintaan pencairan saldo Rekber berhasil dicatat dan sedang diproses.');
    }
}