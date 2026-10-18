
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KycController;
use App\Http\Controllers\CatalogController;

Route::redirect('/', '/camhub');

Route::prefix('camhub')->name('camhub.')->group(function () {
    Route::view('/', 'camhub.home')->name('home');

    Route::view('/katalog', 'camhub.katalog-produk')
        ->name('katalog');

    Route::get('/login', [AuthController::class, 'show'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

    Route::get('/api/katalog', [CatalogController::class, 'index'])->name('api.katalog');

    Route::view('/produk/sony-a7-iii', 'camhub.detail-produk')->name('produk.detail');

    Route::middleware('auth')->group(function () {
        Route::view('/dashboard-mitra', 'camhub.dashboard-mitra')
            ->name('dashboard-mitra');

        Route::view('/dashboard-pelanggan', 'camhub.dashboard-pelanggan')
            ->name('dashboard-pelanggan');

        Route::view('/dashboard-admin', 'camhub.dashboard-admin')
            ->name('dashboard-admin');

        Route::get('/booking', fn () => view('camhub.booking-checkout'))
            ->name('booking');

        Route::get('/kyc', [KycController::class, 'show'])->name('kyc');
        Route::post('/kyc', [KycController::class, 'store'])->name('kyc.store');

        Route::post('/booking', function () {
            request()->validate([
                'nama' => ['required', 'string', 'max:255'],
                'nomor_hp' => ['required', 'string', 'max:30'],
                'alamat' => ['required', 'string', 'max:1000'],
                'tanggal_mulai' => ['required', 'date', 'after_or_equal:today'],
                'tanggal_kembali' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
                'metode_pembayaran' => ['required', 'in:transfer,ewallet'],
            ]);

            return redirect()->route('camhub.booking')
                ->with('success', 'Data booking berhasil diterima.');
        })->name('booking.store');
    });
});
