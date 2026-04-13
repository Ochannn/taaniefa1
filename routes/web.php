<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\LaporanController;
use App\Http\Middleware\RoleMiddleware;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::middleware('auth')->group(function () {
    Route::get('/home', [AuthController::class, 'home'])->name('home');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/ajax/dashboard', function () {
        return view('home.dashboard');
    })->name('ajax.dashboard');

    Route::get('/ajax/customer/profile', [AuthController::class, 'customerProfile'])->name('customer.profile');
    Route::post('/ajax/customer/profile/update', [AuthController::class, 'updateCustomerProfile'])->name('customer.profile.update');
});

Route::middleware(['auth', 'role:KRL001'])->group(function () {
    Route::get('/ajax/master/{module}', [MasterController::class, 'index'])->name('ajax.master.index');
    Route::post('/ajax/master/{module}/store', [MasterController::class, 'store'])->name('ajax.master.store');
    Route::post('/ajax/master/{module}/update/{id}', [MasterController::class, 'update'])->name('ajax.master.update');
    Route::delete('/ajax/master/{module}/delete/{id}', [MasterController::class, 'destroy'])->name('ajax.master.delete');
});

Route::middleware(['auth', 'role:KRL001,KRL002'])->group(function () {
    Route::get('/ajax/transaksi/pembelian', [TransaksiController::class, 'pembelian'])->name('ajax.transaksi.pembelian');
    Route::post('/ajax/transaksi/pembelian/store', [TransaksiController::class, 'storePembelian'])->name('ajax.transaksi.pembelian.store');

    Route::get('/ajax/transaksi/pembelian/data', [TransaksiController::class, 'listPembelian'])->name('ajax.transaksi.pembelian.data');
    Route::get('/ajax/transaksi/pembelian/show/{kode_pembelian}', [TransaksiController::class, 'showPembelian'])->name('ajax.transaksi.pembelian.show');
    Route::post('/ajax/transaksi/pembelian/update/{kode_pembelian}', [TransaksiController::class, 'updatePembelian'])->name('ajax.transaksi.pembelian.update');
    Route::delete('/ajax/transaksi/pembelian/delete/{kode_pembelian}', [TransaksiController::class, 'deletePembelian'])->name('ajax.transaksi.pembelian.delete');
});

Route::middleware(['auth', 'role:KRL001,KRL002,KRL003'])->group(function () {
    Route::get('/ajax/transaksi/penjualan', [TransaksiController::class, 'penjualan'])->name('ajax.transaksi.penjualan');
    Route::post('/ajax/transaksi/penjualan/store', [TransaksiController::class, 'storePenjualan'])->name('ajax.transaksi.penjualan.store');
    Route::get('/ajax/transaksi/penjualan/data', [TransaksiController::class, 'listPenjualan'])->name('ajax.transaksi.penjualan.data');
    Route::get('/ajax/transaksi/penjualan/show/{kode_pesanan}', [TransaksiController::class, 'showPenjualan'])->name('ajax.transaksi.penjualan.show');
    Route::post('/ajax/transaksi/penjualan/update/{kode_pesanan}', [TransaksiController::class, 'updatePenjualan'])->name('ajax.transaksi.penjualan.update');
    Route::delete('/ajax/transaksi/penjualan/delete/{kode_pesanan}', [TransaksiController::class, 'deletePenjualan'])->name('ajax.transaksi.penjualan.delete');

    Route::get('/transaksi-penjualan', [TransaksiController::class, 'penjualan'])->name('transaksi.penjualan');
});

Route::get('/ajax/laporan/stok', [LaporanController::class, 'stokBarang'])->name('ajax.laporan.stok');

Route::get('/tes-db', function () {
    try {
        DB::connection()->getPdo();
        return 'Database telah terhubung';
    } catch (\Exception $e) {
        return 'Database tidak terhubung: ' . $e->getMessage();
    }
});