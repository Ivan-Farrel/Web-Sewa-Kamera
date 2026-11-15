<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\KycController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\VendorController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

Route::redirect('/', '/home')->name('camhub.home');
Route::get('/home', [ProductController::class, 'index'])->name('home');

// ==================== AUTENTIKASI (LOGIN, REGISTER, LOGOUT) ====================
Route::get('/login', [AuthController::class, 'show'])->name('camhub.login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();
        $role = Auth::user()->role ?? 'customer';

        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        if ($role === 'vendor') {
            return redirect()->route('vendor.dashboard');
        }

        // Penyewa / Pelanggan langsung ke Beranda Utama (/home)
        return redirect()->route('home');
    }

    return back()->withErrors([
        'email' => 'Email atau password yang Anda masukkan salah.',
    ])->onlyInput('email');
})->name('login');

Route::post('/register', function (Request $request) {
    $validated = $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|unique:users,email',
        'phone'    => 'nullable|string|max:30',
        'role'     => 'required|in:customer,vendor',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $userData = [
        'name'     => $validated['name'],
        'email'    => $validated['email'],
        'password' => Hash::make($validated['password']),
    ];

    if (Schema::hasColumn('users', 'role')) {
        $userData['role'] = $validated['role'];
    }
    if (Schema::hasColumn('users', 'phone')) {
        $userData['phone'] = $validated['phone'] ?? null;
    }

    $user = User::create($userData);
    Auth::login($user);

    // Jika daftar sebagai Mitra -> langsung ke Dashboard Mitra
    if ($validated['role'] === 'vendor') {
        return redirect()->route('vendor.dashboard')
            ->with('success', 'Selamat datang Mitra CamHub! Silakan unggah unit kamera atau lensa Anda.');
    }

    // Jika daftar sebagai Penyewa/Pelanggan (Opsional) -> langsung ke Beranda Utama (/home)
    return redirect()->route('home')
        ->with('success', 'Akun berhasil dibuat! Selamat menyewa di CamHub.');
})->name('register');

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ==================== KATALOG PRODUK ====================
Route::get('/products', [CatalogController::class, 'index'])->name('products.index');
Route::get('/products/detail', [CatalogController::class, 'show']);
Route::get('/products/{id}', [CatalogController::class, 'show'])->name('products.show');

// ==================== SEWA / BOOKING (TAMU & MEMBER) ====================
Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
Route::get('/bookings/success/{code}', [BookingController::class, 'success'])->name('bookings.success');
Route::post('/bookings/{code}/pay', [BookingController::class, 'simulatePayment'])->name('bookings.pay');
Route::post('/bookings/{code}/complete', [BookingController::class, 'completeRental'])->name('bookings.complete');

// ==================== AREA LOGIN KHUSUS (PELANGGAN, MITRA, ADMIN) ====================
Route::middleware('auth')->group(function () {
    // Jika akses /dashboard umum, arahkan otomatis sesuai role
    Route::get('/dashboard', function () {
        $role = Auth::user()->role ?? 'customer';
        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        if ($role === 'vendor') {
            return redirect()->route('vendor.dashboard');
        }
        return redirect()->route('home');
    })->name('dashboard');

    Route::get('/customer', [BookingController::class, 'dashboard'])->name('camhub.dashboard-pelanggan');

    // DASHBOARD MITRA (C2C Sewa/Jual & C2B Titip Jual)
    Route::get('/vendor', [VendorController::class, 'index'])->name('vendor.dashboard');
    Route::get('/vendor/dashboard', [VendorController::class, 'index']);
    Route::post('/vendor/products', [VendorController::class, 'storeProduct'])->name('vendor.products.store');
    Route::delete('/vendor/products/{id}', [VendorController::class, 'destroyProduct'])->name('vendor.products.destroy');
    Route::post('/vendor/bookings/{id}/confirm', [VendorController::class, 'confirmBooking'])->name('vendor.bookings.confirm');
    Route::post('/vendor/bookings/{id}/return', [VendorController::class, 'confirmReturn'])->name('vendor.bookings.return');
    Route::post('/vendor/kyc', [VendorController::class, 'uploadKyc'])->name('vendor.kyc.store');
    Route::post('/vendor/withdraw', [VendorController::class, 'withdraw'])->name('vendor.withdraw');

    // DASHBOARD ADMIN (Kelola Pengguna, Verifikasi KYC, Kontrol Rekber)
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
    Route::post('/admin/kyc/{id}', [AdminController::class, 'updateKycStatus'])->name('admin.kyc.update');
    Route::post('/admin/bookings/{code}', [AdminController::class, 'updateBookingStatus'])->name('admin.bookings.update');

    // KYC UMUM
    Route::get('/kyc', [KycController::class, 'show'])->name('camhub.kyc');
    Route::post('/kyc', [KycController::class, 'store'])->name('kyc.store');
});