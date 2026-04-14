<?php

namespace App\Http\Controllers;

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
}