<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function stokBarang()
    {
<<<<<<< HEAD
        $data = DB::table('laporan_stok_barang')
            ->orderBy('kode_barang', 'asc')
=======
        $pembelian = DB::table('transaksi_pembelian_detail')
            ->select('kode_barang', DB::raw('SUM(qty) as stok_masuk'))
            ->groupBy('kode_barang');

        $penjualan = DB::table('transaksi_penjualan_detail')
            ->select('kode_barang', DB::raw('SUM(qty) as stok_keluar'))
            ->groupBy('kode_barang');

        $data = DB::table('master_barang as b')
            ->leftJoinSub($pembelian, 'pb', function ($join) {
                $join->on('b.kode_barang', '=', 'pb.kode_barang');
            })
            ->leftJoinSub($penjualan, 'pj', function ($join) {
                $join->on('b.kode_barang', '=', 'pj.kode_barang');
            })
            ->select(
                'b.kode_barang',
                'b.nama_barang',
                DB::raw('COALESCE(pb.stok_masuk, 0) as stok_masuk'),
                DB::raw('COALESCE(pj.stok_keluar, 0) as stok_keluar'),
                DB::raw('COALESCE(b.kapasitas, 0) as stok_akhir'),
                DB::raw('(COALESCE(b.kapasitas, 0) - COALESCE(pb.stok_masuk, 0) + COALESCE(pj.stok_keluar, 0)) as stok_awal')
            )
            ->orderBy('b.kode_barang', 'asc')
>>>>>>> 6990765a2e2528e4dbb9677a1ca74a62e1b48e34
            ->get();

        return view('laporan.laporan_stok_barang', compact('data'));
    }
}