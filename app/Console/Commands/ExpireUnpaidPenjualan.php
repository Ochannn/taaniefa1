<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpireUnpaidPenjualan extends Command
{
    protected $signature = 'penjualan:expire-unpaid';
    protected $description = 'Menggagalkan pembayaran penjualan yang melewati batas waktu dan mengembalikan stok barang.';

    public function handle(): int
    {
        $pembayarans = DB::table('pembayaran_penjualan')
            ->where('metode_pembayaran', 'Midtrans')
            ->where('status_pembayaran', 'Belum Dibayar')
            ->whereNotNull('expired_at')
            ->where('expired_at', '<=', now())
            ->get();

        foreach ($pembayarans as $pembayaran) {
            DB::transaction(function () use ($pembayaran) {
                $lockedPembayaran = DB::table('pembayaran_penjualan')
                    ->where('kode_pembayaran', $pembayaran->kode_pembayaran)
                    ->lockForUpdate()
                    ->first();

                if (!$lockedPembayaran) {
                    return;
                }

                if ($lockedPembayaran->status_pembayaran === 'Lunas') {
                    return;
                }

                if (!empty($lockedPembayaran->stok_dikembalikan_at)) {
                    return;
                }

                $penjualan = DB::table('transaksi_penjualan')
                    ->where('kode_pesanan', $lockedPembayaran->kode_pesanan)
                    ->lockForUpdate()
                    ->first();

                if (!$penjualan || $penjualan->status_pembayaran === 'Lunas') {
                    return;
                }

                $details = DB::table('transaksi_penjualan_detail')
                    ->where('kode_pesanan', $lockedPembayaran->kode_pesanan)
                    ->lockForUpdate()
                    ->get();

                foreach ($details as $detail) {
                    DB::table('master_barang')
                        ->where('kode_barang', $detail->kode_barang)
                        ->increment('kapasitas', (float) $detail->qty);
                }

                DB::table('pembayaran_penjualan')
                    ->where('kode_pembayaran', $lockedPembayaran->kode_pembayaran)
                    ->update([
                        'status_pembayaran'    => 'Gagal Bayar',
                        'transaction_status'   => 'expire',
                        'stok_dikembalikan_at' => now(),
                        'updated_at'           => now(),
                    ]);

                DB::table('transaksi_penjualan')
                    ->where('kode_pesanan', $lockedPembayaran->kode_pesanan)
                    ->update([
                        'status_pembayaran' => 'Gagal Bayar',
                        'status_pesanan'    => 'Batal',
                    ]);
            });
        }

        $this->info('Proses expire pembayaran selesai.');

        return self::SUCCESS;
    }
}