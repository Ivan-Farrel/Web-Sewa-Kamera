<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        $productId = $request->query('product_id');
        $product = $productId ? Product::findOrFail($productId) : Product::first();
        abort_if(!$product, 404, 'Produk belum tersedia.');

        $duration = $request->query('duration', '24h');
        $durationMap = $this->durationMap($product);
        if (!isset($durationMap[$duration])) {
            $duration = '24h';
        }
        $package = $durationMap[$duration];
        $rate = $package['price'];
        $durationLabel = $package['label'];
        $days = $package['days'];

        $start = $request->query('start') ?: now()->toDateString();
        $end = date('Y-m-d', strtotime($start . ' +' . $package['add_days'] . ' days'));

        $adminFee = 5000;
        $total = $rate + $adminFee;

        return view('bookings.create', compact('product', 'duration', 'durationLabel', 'rate', 'adminFee', 'total', 'start', 'end', 'days'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['nullable', 'exists:products,id'],
            'nama' => ['required', 'string', 'max:255'],
            'telepon' => ['required', 'string', 'max:30'],
            'alamat' => ['required', 'string', 'max:1000'],
            'pengambilan' => ['required', 'in:pickup,kirim'],
            'pembayaran' => ['required', 'in:escrow,transfer,ewallet'],
            'tanggal_mulai' => ['required', 'date', 'after_or_equal:today'],
            'durasi' => ['required', 'string'],
            'ktp_photo' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'guarantee_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ], [
            'ktp_photo.required' => 'Foto KTP / Kartu Identitas wajib dilampirkan sebagai jaminan sewa.',
            'ktp_photo.image' => 'File KTP harus berupa gambar (JPG, PNG, atau WEBP).',
            'ktp_photo.max' => 'Ukuran file KTP maksimal 5MB.',
        ]);

        $product = !empty($data['product_id']) ? Product::findOrFail($data['product_id']) : Product::first();
        abort_if(!$product, 404, 'Produk belum tersedia.');

        $duration = $data['durasi'];
        $package = $this->durationMap($product)[$duration] ?? null;
        if (!$package) {
            return back()->withErrors(['durasi' => 'Paket durasi tidak valid.'])->withInput();
        }

        $days = $package['days'];
        $rate = $package['price'];
        $adminFee = 5000;
        $total = $rate + $adminFee;
        $endDate = date('Y-m-d', strtotime($data['tanggal_mulai'] . ' +' . $package['add_days'] . ' days'));

        $uploadDir = public_path('uploads/guarantees');
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }

        // Upload Foto KTP
        $ktpPath = null;
        if ($request->hasFile('ktp_photo')) {
            $file = $request->file('ktp_photo');
            $filename = 'ktp_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $target = $uploadDir . DIRECTORY_SEPARATOR . $filename;
            try {
                $file->move($uploadDir, $filename);
            } catch (\Throwable $e) {
                copy($file->getRealPath(), $target);
            }
            $ktpPath = 'uploads/guarantees/' . $filename;
        }

        // Upload Dokumen Jaminan Tambahan (opsional)
        $guaranteePath = null;
        if ($request->hasFile('guarantee_photo')) {
            $file = $request->file('guarantee_photo');
            $filename = 'guarantee_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $target = $uploadDir . DIRECTORY_SEPARATOR . $filename;
            try {
                $file->move($uploadDir, $filename);
            } catch (\Throwable $e) {
                copy($file->getRealPath(), $target);
            }
            $guaranteePath = 'uploads/guarantees/' . $filename;
        }

        do {
            $bookingCode = 'CH-' . strtoupper(Str::random(10));
        } while (Booking::where('booking_code', $bookingCode)->exists());

        $booking = Booking::create([
            'user_id' => auth()->id() ?? null,
            'product_id' => $product->id,
            'booking_code' => $bookingCode,
            'product_name' => $product->name . ' (' . $package['label'] . ')',
            'customer_name' => $data['nama'],
            'customer_phone' => $data['telepon'],
            'customer_address' => $data['alamat'],
            'delivery_method' => $data['pengambilan'],
            'payment_method' => $data['pembayaran'],
            'ktp_photo' => $ktpPath,
            'guarantee_photo' => $guaranteePath,
            'start_date' => $data['tanggal_mulai'],
            'end_date' => $endDate,
            'days' => $days,
            'daily_rate' => $rate,
            'total' => $total,
            'status' => 'Menunggu verifikasi jaminan',
        ]);

        return redirect()->route('bookings.success', $booking->booking_code)
            ->with('success', 'Pengajuan sewa kamu berhasil dikirim!');
    }

    public function success(string $code)
    {
        $booking = Booking::where('booking_code', $code)->firstOrFail();

        return view('bookings.success', compact('booking'));
    }

    public function simulatePayment(string $code)
    {
        $booking = Booking::where('booking_code', $code)->firstOrFail();
        abort_unless($booking->status === 'Menunggu verifikasi jaminan', 403);
        $booking->update([
            'status' => 'Lunas (Siap Diambil / Dikirim)',
        ]);

        return back()->with('success', 'Simulasi pembayaran sukses! Status pesanan kini telah lunas.');
    }

    public function completeRental(string $code)
    {
        abort_unless(auth()->user()?->role === 'admin', 403);
        $booking = Booking::where('booking_code', $code)->firstOrFail();
        abort_unless(
            str_contains($booking->status, 'Lunas') || str_contains($booking->status, 'Aktif'),
            409,
            'Booking harus sudah lunas atau sedang disewa sebelum dapat diselesaikan.'
        );
        $booking->update(['status' => 'Selesai']);

        return back()->with('success', 'Booking ditandai selesai.');
    }

    private function durationMap(Product $product): array
    {
        return [
            '8h' => ['label' => '8 Jam', 'days' => 1, 'add_days' => 0, 'price' => $product->price_8h],
            '12h' => ['label' => '12 Jam', 'days' => 1, 'add_days' => 0, 'price' => $product->price_12h],
            '24h' => ['label' => '24 Jam', 'days' => 1, 'add_days' => 1, 'price' => $product->price_24h],
            '2d' => ['label' => '2 Hari', 'days' => 2, 'add_days' => 2, 'price' => $product->price_2d],
        ];
    }

    public function dashboard(Request $request)
    {
        $user = $request->user() ?? auth()->user();
        $bookings = $user ? $user->bookings()->latest()->get() : collect();

        return view('customer.dashboard', compact('bookings'));
    }
}
