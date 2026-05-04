<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function stokBarang()
    {
        $data = DB::table('laporan_stok_barang')
            ->orderBy('kode_barang', 'asc')
            ->get();

        return view('laporan.laporan_stok_barang', compact('data'));
    }


    public function penjualan(Request $request)
    {
        $tanggalAwal = $request->get('tanggal_awal');
        $tanggalAkhir = $request->get('tanggal_akhir');

        $query = DB::table('laporan_penjualan');

        if ($tanggalAwal && $tanggalAkhir) {
            $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
        }

        $data = $query
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('laporan.laporan_penjualan', [
            'data' => $data,
            'tanggalAwal' => $tanggalAwal,
            'tanggalAkhir' => $tanggalAkhir,
        ]);
    }

    public function pengeluaran(Request $request)
    {
        $tanggalAwal = $request->get('tanggal_awal');
        $tanggalAkhir = $request->get('tanggal_akhir');

        $query = DB::table('laporan_pengeluaran');

        if ($tanggalAwal && $tanggalAkhir) {
            $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
        }

        $data = $query
            ->orderBy('tanggal', 'desc')
            ->orderBy('kode_pembelian', 'desc')
            ->get();

        return view('laporan.laporan_pengeluaran', [
            'data' => $data,
            'tanggalAwal' => $tanggalAwal,
            'tanggalAkhir' => $tanggalAkhir,
        ]);
    }

    public function pemasukan(Request $request)
    {
        $tanggalAwal = $request->get('tanggal_awal');
        $tanggalAkhir = $request->get('tanggal_akhir');

        $query = DB::table('laporan_pemasukan');

        if ($tanggalAwal && $tanggalAkhir) {
            $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
        }

        $data = $query
            ->orderBy('tanggal', 'desc')
            ->orderBy('kode_pesanan', 'desc')
            ->get();

        return view('laporan.laporan_pemasukan', [
            'data' => $data,
            'tanggalAwal' => $tanggalAwal,
            'tanggalAkhir' => $tanggalAkhir,
        ]);
    }

    public function pengiriman(Request $request)
    {
        $tanggalAwal = $request->get('tanggal_awal');
        $tanggalAkhir = $request->get('tanggal_akhir');

        $query = DB::table('laporan_pengiriman');

        if ($tanggalAwal && $tanggalAkhir) {
            $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
        }

        $data = $query
            ->orderBy('tanggal', 'desc')
            ->orderBy('kode_pesanan', 'desc')
            ->get();

        return view('laporan.laporan_pengiriman', [
            'data' => $data,
            'tanggalAwal' => $tanggalAwal,
            'tanggalAkhir' => $tanggalAkhir,
        ]);
    }

    public function presentasiReject(Request $request)
    {
        $tanggalAwal = $request->get('tanggal_awal');
        $tanggalAkhir = $request->get('tanggal_akhir');

        $query = DB::table('laporan_presentasi_reject');

        if ($tanggalAwal && $tanggalAkhir) {
            $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
        }

        $data = $query
            ->orderBy('tanggal', 'desc')
            ->orderBy('kode_barang', 'asc')
            ->get();

        return view('laporan.laporan_presentasi_reject', [
            'data' => $data,
            'tanggalAwal' => $tanggalAwal,
            'tanggalAkhir' => $tanggalAkhir,
        ]);
    }

    public function returPembelian(Request $request)
    {
        $tanggalAwal = $request->get('tanggal_awal');
        $tanggalAkhir = $request->get('tanggal_akhir');

        $query = DB::table('laporan_retur_pembelian');

        if ($tanggalAwal && $tanggalAkhir) {
            $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
        }

        $data = $query
            ->orderBy('tanggal', 'desc')
            ->orderBy('kode_barang', 'asc')
            ->get();

        return view('laporan.laporan_retur_pembelian', [
            'data' => $data,
            'tanggalAwal' => $tanggalAwal,
            'tanggalAkhir' => $tanggalAkhir,
        ]);
    }

    public function returPenjualan(Request $request)
    {
        $tanggalAwal = $request->get('tanggal_awal');
        $tanggalAkhir = $request->get('tanggal_akhir');

        $query = DB::table('laporan_retur_penjualan');

        if ($tanggalAwal && $tanggalAkhir) {
            $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
        }

        $data = $query
            ->orderBy('tanggal', 'desc')
            ->orderBy('kode_pesanan', 'desc')
            ->get();

        return view('laporan.laporan_retur_penjualan', [
            'data' => $data,
            'tanggalAwal' => $tanggalAwal,
            'tanggalAkhir' => $tanggalAkhir,
        ]);
    }

    public function keuangan(Request $request)
    {
        $tanggalAwal = $request->get('tanggal_awal');
        $tanggalAkhir = $request->get('tanggal_akhir');

        $query = DB::table('laporan_keuangan');

        if ($tanggalAwal && $tanggalAkhir) {
            $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
        }

        $data = $query
            ->orderBy('tanggal', 'desc')
            ->get();

        $totalPemasukan = $data->sum('total_pemasukan');
        $totalPengeluaran = $data->sum('total_pengeluaran');
        $totalLabaRugi = $totalPemasukan - $totalPengeluaran;

        return view('laporan.laporan_keuangan', [
            'data' => $data,
            'tanggalAwal' => $tanggalAwal,
            'tanggalAkhir' => $tanggalAkhir,
            'totalPemasukan' => $totalPemasukan,
            'totalPengeluaran' => $totalPengeluaran,
            'totalLabaRugi' => $totalLabaRugi,
        ]);
    }

    public function pembelian(Request $request)
    {
        $tanggalAwal = $request->get('tanggal_awal');
        $tanggalAkhir = $request->get('tanggal_akhir');

        $query = DB::table('laporan_pembelian');

        if ($tanggalAwal && $tanggalAkhir) {
            $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
        }

        $data = $query
            ->orderBy('tanggal', 'desc')
            ->orderBy('kode_pembelian', 'desc')
            ->get();

        return view('laporan.laporan_pembelian', [
            'data' => $data,
            'tanggalAwal' => $tanggalAwal,
            'tanggalAkhir' => $tanggalAkhir,
        ]);
    }

    public function logAktivitas(Request $request)
    {
        $tanggalAwal = $request->get('tanggal_awal');
        $tanggalAkhir = $request->get('tanggal_akhir');

        $query = DB::table('log_aktivitas');

        if ($tanggalAwal && $tanggalAkhir) {
            $query->whereDate('created_at', '>=', $tanggalAwal)
                ->whereDate('created_at', '<=', $tanggalAkhir);
        }

        $data = $query
            ->orderBy('created_at', 'desc')
            ->get();

        return view('laporan.log_aktivitas', [
            'data' => $data,
            'tanggalAwal' => $tanggalAwal,
            'tanggalAkhir' => $tanggalAkhir,
        ]);
    }
}