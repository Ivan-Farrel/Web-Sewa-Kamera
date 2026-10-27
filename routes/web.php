<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\KycController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/home')->name('camhub.home');
Route::view('/home', 'home')->name('home');

Route::get('/login', [AuthController::class, 'show'])->name('camhub.login');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/products', [CatalogController::class, 'index'])->name('products.index');
Route::get('/products/detail', [CatalogController::class, 'show'])->name('products.show');

Route::middleware('auth')->group(function () {
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/customer', [BookingController::class, 'dashboard'])->name('camhub.dashboard-pelanggan');
    Route::get('/vendor', fn () => auth()->user()->role === 'vendor' ? view('vendor.dashboard') : abort(403))->name('vendor.dashboard');
    Route::get('/admin', fn () => auth()->user()->role === 'admin' ? view('admin.dashboard') : abort(403))->name('admin.dashboard');
    Route::get('/kyc', [KycController::class, 'show'])->name('camhub.kyc');
    Route::post('/kyc', [KycController::class, 'store'])->name('kyc.store');
});
