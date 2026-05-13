<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\NotifikasiController;

// Halaman awal / landing page
Route::get('/', function () {
    return view('auth.dashboard');
})->name('dashboard.awal');

// Halaman lihat barang
Route::get('/lihatbarang', function () {
    return view('auth.lihatbarang');
})->name('lihatbarang');

// Halaman tentang kami
Route::get('/tentangkami', function () {
    return view('auth.tentangkami');
})->name('tentangkami');

// Route untuk guest
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Webhook Midtrans harus di luar middleware auth
Route::post('/midtrans/notification', [TransaksiController::class, 'midtransNotification'])
    ->name('midtrans.notification');

// Route setelah login
Route::middleware('auth')->group(function () {
    Route::get('/home', [AuthController::class, 'home'])->name('home');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/ajax/dashboard', function () {
        $pesananPerluValidasi = DB::table('transaksi_penjualan')
            ->where('status_pembayaran', 'Menunggu Validasi')
            ->count();

        $transaksiHariIni = DB::table('transaksi_penjualan')
            ->whereDate('tgl_pesanan', now()->toDateString())
            ->count();

        $pengirimanDiproses = DB::table('transaksi_penjualan')
            ->whereIn('status_pesanan', ['Diproses', 'Dikirim'])
            ->count();

        $returMasuk = DB::table('retur_penjualan')
            ->count();

        return view('home.dashboard', compact(
            'pesananPerluValidasi',
            'transaksiHariIni',
            'pengirimanDiproses',
            'returMasuk'
        ));
    })->name('ajax.dashboard');

    Route::get('/ajax/customer/profile', [AuthController::class, 'customerProfile'])
        ->name('customer.profile');

    Route::post('/ajax/customer/profile/update', [AuthController::class, 'updateCustomerProfile'])
        ->name('customer.profile.update');

    // Notifikasi
    Route::get('/ajax/notifikasi', [NotifikasiController::class, 'index'])
        ->name('ajax.notifikasi.index');

    Route::post('/ajax/notifikasi/baca-semua', [NotifikasiController::class, 'markAllAsRead'])
        ->name('ajax.notifikasi.baca_semua');

    Route::post('/ajax/notifikasi/{id}/dibaca', [NotifikasiController::class, 'markAsRead'])
        ->name('ajax.notifikasi.dibaca');
});

// Role KRL001
Route::middleware(['auth', 'role:KRL001'])->group(function () {
    Route::get('/ajax/master/{module}', [MasterController::class, 'index'])
        ->name('ajax.master.index');

    Route::post('/ajax/master/{module}/store', [MasterController::class, 'store'])
        ->name('ajax.master.store');

    Route::post('/ajax/master/{module}/update/{id}', [MasterController::class, 'update'])
        ->name('ajax.master.update');

    Route::delete('/ajax/master/{module}/delete/{id}', [MasterController::class, 'destroy'])
        ->name('ajax.master.delete');
});

// Role KRL001 dan KRL002
Route::middleware(['auth', 'role:KRL001,KRL002'])->group(function () {
    Route::get('/ajax/transaksi/pembelian', [TransaksiController::class, 'pembelian'])
        ->name('ajax.transaksi.pembelian');

    Route::post('/ajax/transaksi/pembelian/store', [TransaksiController::class, 'storePembelian'])
        ->name('ajax.transaksi.pembelian.store');

    Route::get('/ajax/transaksi/pembelian/data', [TransaksiController::class, 'listPembelian'])
        ->name('ajax.transaksi.pembelian.data');

    Route::get('/ajax/transaksi/pembelian/show/{kode_pembelian}', [TransaksiController::class, 'showPembelian'])
        ->name('ajax.transaksi.pembelian.show');

    Route::post('/ajax/transaksi/pembelian/update/{kode_pembelian}', [TransaksiController::class, 'updatePembelian'])
        ->name('ajax.transaksi.pembelian.update');

    Route::delete('/ajax/transaksi/pembelian/delete/{kode_pembelian}', [TransaksiController::class, 'deletePembelian'])
        ->name('ajax.transaksi.pembelian.delete');

    Route::get('/ajax/transaksi/retur-pembelian', [TransaksiController::class, 'returPembelian'])
        ->name('ajax.transaksi.retur.pembelian');

    Route::post('/ajax/transaksi/retur-pembelian/store', [TransaksiController::class, 'storeReturPembelian'])
        ->name('ajax.transaksi.retur.pembelian.store');

    Route::get('/ajax/transaksi/retur-pembelian/data', [TransaksiController::class, 'listReturPembelian'])
        ->name('ajax.transaksi.retur.pembelian.data');

    Route::get('/ajax/transaksi/retur-pembelian/pembelian/{kode_pembelian}', [TransaksiController::class, 'getPembelianUntukRetur'])
        ->name('ajax.transaksi.retur.pembelian.detail');
});

// Role KRL001, KRL002, dan KRL003
Route::middleware(['auth', 'role:KRL001,KRL002,KRL003'])->group(function () {
    Route::get('/ajax/transaksi/penjualan', [TransaksiController::class, 'penjualan'])
        ->name('ajax.transaksi.penjualan');

    Route::post('/ajax/transaksi/penjualan/store', [TransaksiController::class, 'storePenjualan'])
        ->name('ajax.transaksi.penjualan.store');

    Route::post('/ajax/transaksi/penjualan/midtrans-token/{kode_pesanan}', [TransaksiController::class, 'createSnapTokenPenjualan'])
        ->name('ajax.transaksi.penjualan.midtrans_token');

    Route::get('/ajax/transaksi/penjualan/data', [TransaksiController::class, 'listPenjualan'])
        ->name('ajax.transaksi.penjualan.data');

    Route::get('/ajax/transaksi/penjualan/show/{kode_pesanan}', [TransaksiController::class, 'showPenjualan'])
        ->name('ajax.transaksi.penjualan.show');

    Route::post('/ajax/transaksi/penjualan/update/{kode_pesanan}', [TransaksiController::class, 'updatePenjualan'])
        ->name('ajax.transaksi.penjualan.update');

    Route::delete('/ajax/transaksi/penjualan/delete/{kode_pesanan}', [TransaksiController::class, 'deletePenjualan'])
        ->name('ajax.transaksi.penjualan.delete');

    Route::post('/ajax/transaksi/penjualan/upload-bukti/{kode_pesanan}', [TransaksiController::class, 'uploadBuktiPenjualan'])
        ->name('ajax.transaksi.penjualan.upload_bukti');

    Route::post('/ajax/transaksi/penjualan/validasi-pembayaran/{kode_pesanan}', [TransaksiController::class, 'validasiPembayaranPenjualan'])
        ->name('ajax.transaksi.penjualan.validasi_pembayaran');

    Route::post('/ajax/transaksi/penjualan/tolak-pembayaran/{kode_pesanan}', [TransaksiController::class, 'tolakPembayaranPenjualan'])
        ->name('ajax.transaksi.penjualan.tolak_pembayaran');

    Route::post('/ajax/transaksi/penjualan/update-status/{kode_pesanan}', [TransaksiController::class, 'updateStatusPesananPenjualan'])
        ->name('ajax.transaksi.penjualan.update_status');

    Route::post('/ajax/transaksi/penjualan/custom/approve/{kode_pesanan}', [TransaksiController::class, 'approveCustomPenjualan'])
        ->name('ajax.transaksi.penjualan.custom.approve');

    Route::post('/ajax/transaksi/penjualan/custom/reject/{kode_pesanan}', [TransaksiController::class, 'rejectCustomPenjualan'])
        ->name('ajax.transaksi.penjualan.custom.reject');

    Route::get('/ajax/transaksi/riwayat-penjualan', [TransaksiController::class, 'riwayatPenjualan'])
        ->name('ajax.transaksi.riwayat.penjualan');

    Route::get('/transaksi-penjualan', [TransaksiController::class, 'penjualan'])
        ->name('transaksi.penjualan');

    Route::get('/ajax/transaksi/retur-penjualan', [TransaksiController::class, 'returPenjualan'])
        ->name('ajax.transaksi.retur.penjualan');

    Route::post('/ajax/transaksi/retur-penjualan/store', [TransaksiController::class, 'storeReturPenjualan'])
        ->name('ajax.transaksi.retur.penjualan.store');

    Route::get('/ajax/transaksi/retur-penjualan/data', [TransaksiController::class, 'listReturPenjualan'])
        ->name('ajax.transaksi.retur.penjualan.data');

    Route::get('/ajax/transaksi/retur-penjualan/penjualan/{kode_pesanan}', [TransaksiController::class, 'getPenjualanUntukRetur'])
        ->name('ajax.transaksi.retur.penjualan.detail');

    Route::get('/ajax/rajaongkir/search-destination', [TransaksiController::class, 'searchRajaOngkirDestination'])
        ->name('ajax.rajaongkir.search_destination');

    Route::post('/ajax/rajaongkir/check-ongkir', [TransaksiController::class, 'checkRajaOngkir'])
        ->name('ajax.rajaongkir.check_ongkir');

    Route::post('/ajax/transaksi/penjualan/sync-midtrans-status/{kode_pesanan}', [TransaksiController::class, 'syncMidtransStatusPenjualan'])
        ->name('ajax.transaksi.penjualan.sync_midtrans_status');
});

// Laporan stok
Route::middleware(['auth', 'role:KRL001,KRL002'])->group(function () {
    Route::get('/ajax/laporan/stok', [LaporanController::class, 'stokBarang'])
        ->name('ajax.laporan.stok');

    Route::get('/ajax/laporan/penjualan', [LaporanController::class, 'penjualan'])
        ->name('ajax.laporan.penjualan');

    Route::get('/ajax/laporan/pembelian', [LaporanController::class, 'pembelian'])
        ->name('ajax.laporan.pembelian');

    Route::get('/ajax/laporan/pengeluaran', [LaporanController::class, 'pengeluaran'])
        ->name('ajax.laporan.pengeluaran');

    Route::get('/ajax/laporan/pemasukan', [LaporanController::class, 'pemasukan'])
        ->name('ajax.laporan.pemasukan');

    Route::get('/ajax/laporan/pengiriman', [LaporanController::class, 'pengiriman'])
        ->name('ajax.laporan.pengiriman');

    Route::get('/ajax/laporan/presentasi-reject', [LaporanController::class, 'presentasiReject'])
        ->name('ajax.laporan.presentasi.reject');

    Route::get('/ajax/laporan/retur-penjualan', [LaporanController::class, 'returPenjualan'])
        ->name('ajax.laporan.retur.penjualan');

    Route::get('/ajax/laporan/retur-pembelian', [LaporanController::class, 'returPembelian'])
        ->name('ajax.laporan.retur.pembelian');

    Route::get('/ajax/laporan/keuangan', [LaporanController::class, 'keuangan'])
        ->name('ajax.laporan.keuangan');

    Route::get('/ajax/laporan/log-aktivitas', [LaporanController::class, 'logAktivitas'])
        ->name('ajax.laporan.log.aktivitas');
});

Route::get('/tes-db', function () {
    try {
        DB::connection()->getPdo();
        return 'Database berhasil terhubung.';
    } catch (\Exception $e) {
        return 'Database gagal terhubung: ' . $e->getMessage();
    }
});

Route::get('/tes-count-validasi', function () {
    return DB::table('transaksi_penjualan')
        ->where('status_pembayaran', 'Menunggu Validasi')
        ->count();
});