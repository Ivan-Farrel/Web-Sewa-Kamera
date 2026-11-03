<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookingController extends Controller
{
    private const DAILY_RATE = 250000;

    public function create(Request $request)
    {
        $start = $request->query('start');
        $days = max(1, min(30, (int) $request->query('days', 3)));
        $end = $start ? date('Y-m-d', strtotime($start.' +'.($days - 1).' days')) : null;

        return view('bookings.create', compact('start', 'end', 'days'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'telepon' => ['required', 'string', 'max:30'],
            'alamat' => ['required', 'string', 'max:1000'],
            'pengambilan' => ['required', 'in:pickup,kirim'],
            'pembayaran' => ['required', 'in:escrow,transfer,ewallet'],
            'tanggal_mulai' => ['required', 'date', 'after_or_equal:today'],
            'durasi' => ['required', 'integer', 'min:1', 'max:30'],
        ]);

        $days = (int) $data['durasi'];
        $request->user()->bookings()->create([
            'product_name' => 'Sony A7 III',
            'start_date' => $data['tanggal_mulai'],
            'end_date' => date('Y-m-d', strtotime($data['tanggal_mulai'].' +'.($days - 1).' days')),
            'days' => $days,
            'daily_rate' => self::DAILY_RATE,
            'total' => self::DAILY_RATE * $days + 5000,
            'status' => 'Menunggu pembayaran',
        ]);

        return redirect()->route('camhub.dashboard-pelanggan')->with('success', 'Booking berhasil dibuat. Lanjutkan konfirmasi pembayaran dengan mitra.');
    }

    public function dashboard(Request $request)
    {
        $bookings = $request->user()->bookings()->latest()->get();

        return view('customer.dashboard', compact('bookings'));
    }
}
