<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\KycController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/home')->name('camhub.home');
Route::get('/home', [ProductController::class, 'index'])->name('home');

Route::get('/login', [AuthController::class, 'show'])->name('camhub.login');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/products', [CatalogController::class, 'index'])->name('products.index');
Route::get('/products/detail', [CatalogController::class, 'show']);
Route::get('/products/{id}', [CatalogController::class, 'show'])->name('products.show');

// SEWA / BOOKING (Bisa langsung disewa publik/tamu tanpa perlu login, cukup foto jaminan KTP dll)
Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
Route::get('/bookings/success/{code}', [BookingController::class, 'success'])->name('bookings.success');
Route::post('/bookings/{code}/pay', [BookingController::class, 'simulatePayment'])->name('bookings.pay');
Route::post('/bookings/{code}/complete', [BookingController::class, 'completeRental'])->name('bookings.complete');

// AREA LOGIN KHUSUS: Pembeli (Jual Beli Escrow), Mitra / Titip Jual Kamera, dan Admin
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [ProductController::class, 'index'])->name('dashboard');
    Route::get('/customer', [BookingController::class, 'dashboard'])->name('camhub.dashboard-pelanggan');

    // DASHBOARD MITRA (Penjual Kamera, Titip Jual / Titip Sewa)
    Route::get('/vendor', [VendorController::class, 'dashboard'])->name('vendor.dashboard');
    Route::post('/vendor/products', [VendorController::class, 'storeProduct'])->name('vendor.products.store');
    Route::post('/vendor/withdraw', [VendorController::class, 'withdraw'])->name('vendor.withdraw');

    // DASHBOARD ADMIN (Kelola Pengguna, Verifikasi KYC, Kontrol Rekber)
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/admin/kyc/{id}', [AdminController::class, 'updateKycStatus'])->name('admin.kyc.update');
    Route::post('/admin/bookings/{code}', [AdminController::class, 'updateBookingStatus'])->name('admin.bookings.update');

    Route::get('/kyc', [KycController::class, 'show'])->name('camhub.kyc');
    Route::post('/kyc', [KycController::class, 'store'])->name('kyc.store');
});
