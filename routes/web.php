<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\TransaksiPembelianController;
use App\Http\Controllers\TransaksiPenjualanController;
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
});

Route::middleware(['auth', 'role:KRL001'])->group(function () {
    Route::get('/ajax/master/{module}', [MasterController::class, 'index'])->name('ajax.master.index');
    Route::post('/ajax/master/{module}/store', [MasterController::class, 'store'])->name('ajax.master.store');
    Route::post('/ajax/master/{module}/update/{id}', [MasterController::class, 'update'])->name('ajax.master.update');
    Route::delete('/ajax/master/{module}/delete/{id}', [MasterController::class, 'destroy'])->name('ajax.master.delete');
});

Route::middleware(['auth', 'role:KRL001,KRL002'])->group(function () {
    Route::get('/transaksi-pembelian', [TransaksiPembelianController::class, 'index'])->name('transaksi.pembelian');
});

Route::middleware(['auth', 'role:KRL001,KRL002,KRL003'])->group(function () {
    Route::get('/transaksi-penjualan', [TransaksiPenjualanController::class, 'index'])->name('transaksi.penjualan');
});