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
        $product = $productId ? Product::find($productId) : null;
        if (!$product) {
            $product = Product::where('name', 'like', '%Sony A7 III%')->first() ?? Product::first();
        }

        $duration = $request->query('duration', '24h');
        $durationMap = [
            '8h' => ['label' => '8 Jam', 'days' => 1, 'add_days' => 0, 'price' => $product->price_8h ?? 200000],
            '12h' => ['label' => '12 Jam', 'days' => 1, 'add_days' => 0, 'price' => $product->price_12h ?? 250000],
            '24h' => ['label' => '24 Jam', 'days' => 1, 'add_days' => 1, 'price' => $product->price_24h ?? 300000],
            '2d' => ['label' => '2 Hari', 'days' => 2, 'add_days' => 2, 'price' => $product->price_2d ?? 500000],
        ];

        $package = $durationMap[$duration] ?? $durationMap['24h'];
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

        $product = !empty($data['product_id'])
            ? Product::find($data['product_id'])
            : (Product::where('name', 'like', '%Sony A7 III%')->first() ?? Product::first());

        $duration = $data['durasi'];
        $durationMap = [
            '8h' => ['label' => '8 Jam', 'days' => 1, 'add_days' => 0, 'price' => $product->price_8h ?? 200000],
            '12h' => ['label' => '12 Jam', 'days' => 1, 'add_days' => 0, 'price' => $product->price_12h ?? 250000],
            '24h' => ['label' => '24 Jam', 'days' => 1, 'add_days' => 1, 'price' => $product->price_24h ?? 300000],
            '2d' => ['label' => '2 Hari', 'days' => 2, 'add_days' => 2, 'price' => $product->price_2d ?? 500000],
        ];

        $package = $durationMap[$duration] ?? [
            'label' => is_numeric($duration) ? $duration . ' Hari' : '24 Jam',
            'days' => is_numeric($duration) ? (int)$duration : 1,
            'add_days' => is_numeric($duration) ? (int)$duration : 1,
            'price' => $product->price_24h ?? 250000,
        ];

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

        $bookingCode = 'CH-' . strtoupper(Str::random(6));

        $booking = Booking::create([
            'user_id' => auth()->id() ?? null,
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
        $booking->update([
            'status' => 'Lunas (Siap Diambil / Dikirim)',
        ]);

        return back()->with('success', 'Simulasi pembayaran sukses! Status pesanan kini telah lunas.');
    }

    public function dashboard(Request $request)
    {
        $user = $request->user() ?? auth()->user();
        $bookings = $user ? $user->bookings()->latest()->get() : collect();

        return view('customer.dashboard', compact('bookings'));
    }
}
