<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class TransaksiController extends Controller
{

    public function riwayatPenjualan()
    {
        $user = Auth::user();

        return view('transaksi.riwayat_penjualan', [
            'user' => $user,
        ]);
    }

    public function returPenjualan()
    {
        $user = Auth::user();

        $query = DB::table('transaksi_penjualan as tp')
            ->leftJoin('master_customer as mc', 'tp.kode_customer', '=', 'mc.kode_customer')
            ->select(
                'tp.kode_pesanan',
                'tp.tgl_pesanan',
                'tp.kode_customer',
                'mc.nama_customer',
                'tp.status_pesanan'
            )
            ->where('tp.status_pesanan', 'Selesai')
            ->orderByDesc('tp.tgl_pesanan')
            ->orderByDesc('tp.kode_pesanan');

        if ($this->isCustomerRole($user->kode_role)) {
            $customerAktif = $this->getCustomerAktifByUser();

            if (!$customerAktif || empty($customerAktif->kode_customer)) {
                $penjualans = collect();
            } else {
                $penjualans = $query
                    ->where('tp.kode_customer', $customerAktif->kode_customer)
                    ->get();
            }
        } else {
            $penjualans = $query->get();
        }

        return view('transaksi.retur_penjualan', [
            'kodePreview' => $this->previewKodeReturPenjualan(),
            'penjualans'  => $penjualans,
        ]);
    }  
    public function pembelian()
    {
        $suppliers = DB::table('master_supplier')
            ->select('kode_supplier', 'nama_supplier')
            ->orderBy('nama_supplier')
            ->get();

        $barangs = DB::table('master_barang as mb')
            ->leftJoin('master_kategori_barang as mk', 'mb.kode_kategori', '=', 'mk.kode_kategori')
            ->select(
                'mb.kode_barang',
                'mb.nama_barang',
                'mb.kapasitas',
                'mb.kode_kategori',
                'mk.nama_kategori'
            )
            ->orderBy('mk.nama_kategori')
            ->orderBy('mb.nama_barang')
            ->get();

        $kategoris = DB::table('master_kategori_barang')
            ->select('kode_kategori', 'nama_kategori')
            ->orderBy('nama_kategori')
            ->get();

        return view('transaksi.transaksi_pembelian', [
            'kodePreview' => $this->previewKodePembelian(),
            'suppliers'   => $suppliers,
            'barangs'     => $barangs,
            'kategoris'   => $kategoris,
        ]);
    }

    public function storePembelian(Request $request)
    {
        $request->validate([
            'tgl_pembelian'     => 'required|date',
            'kode_supplier'     => 'required|string',
            'catatan_pembelian' => 'nullable|string',
            'items'             => 'required|array|min:1',
            'items.*.kode_barang'  => 'required|string',
            'items.*.nama_barang'  => 'required|string',
            'items.*.qty'          => 'required|numeric|min:1',
            'items.*.harga_barang' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();

        if (!$user || empty($user->kode_user)) {
            return response()->json([
                'success' => false,
                'message' => 'Kode user login tidak ditemukan.'
            ], 422);
        }

        try {
            DB::transaction(function () use ($request, $user, &$kodePembelian, &$totalPembelian) {
                $kodePembelian = $this->generateKodePembelianLocked();

                $detailRows = [];
                $totalPembelian = 0;

                foreach ($request->items as $item) {
                    $qty = (float) $item['qty'];
                    $harga = (float) $item['harga_barang'];
                    $subtotal = $qty * $harga;

                    $totalPembelian += $subtotal;

                    $detailRows[] = [
                        'kode_pembelian'  => $kodePembelian,
                        'kode_barang'     => $item['kode_barang'],
                        'nama_barang'     => $item['nama_barang'],
                        'qty'             => $qty,
                        'harga_barang'    => $harga,
                        'subtotal_barang' => $subtotal,
                        'date_entry'      => now(),
                    ];
                }

                DB::table('transaksi_pembelian')->insert([
                    'kode_pembelian'    => $kodePembelian,
                    'tgl_pembelian'     => $request->tgl_pembelian,
                    'kode_supplier'     => $request->kode_supplier,
                    'kode_user'         => $user->kode_user,
                    'total_pembelian'   => $totalPembelian,
                    'catatan_pembelian' => $request->catatan_pembelian,
                ]);

                DB::table('transaksi_pembelian_detail')->insert($detailRows);

                foreach ($request->items as $item) {
                    DB::table('master_barang')
                        ->where('kode_barang', $item['kode_barang'])
                        ->increment('kapasitas', (float) $item['qty']);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Transaksi pembelian berhasil disimpan.',
                'kode_pembelian' => $kodePembelian,
                'total_pembelian' => $totalPembelian,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan transaksi: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function previewKodePembelian()
    {
        $lastKode = DB::table('transaksi_pembelian')
            ->orderByDesc('kode_pembelian')
            ->value('kode_pembelian');

        if (!$lastKode) {
            return 'KBL001';
        }

        $angka = (int) substr($lastKode, 3);
        return 'KBL' . str_pad($angka + 1, 3, '0', STR_PAD_LEFT);
    }

    private function generateKodePembelianLocked()
    {
        $lastKode = DB::table('transaksi_pembelian')
            ->lockForUpdate()
            ->orderByDesc('kode_pembelian')
            ->value('kode_pembelian');

        if (!$lastKode) {
            return 'KBL001';
        }

        $angka = (int) substr($lastKode, 3);
        return 'KBL' . str_pad($angka + 1, 3, '0', STR_PAD_LEFT);
    }

    public function listPembelian()
    {
        $data = DB::table('transaksi_pembelian as tp')
            ->leftJoin('master_supplier as ms', 'tp.kode_supplier', '=', 'ms.kode_supplier')
            ->select(
                'tp.kode_pembelian',
                'tp.tgl_pembelian',
                'tp.kode_supplier',
                'ms.nama_supplier',
                'tp.catatan_pembelian'
            )
            ->orderByDesc('tp.tgl_pembelian')
            ->orderByDesc('tp.kode_pembelian')
            ->get();

        return response()->json(['data' => $data]);
    }

    public function showPembelian($kode_pembelian)
    {
        $header = DB::table('transaksi_pembelian as tp')
            ->leftJoin('master_supplier as ms', 'tp.kode_supplier', '=', 'ms.kode_supplier')
            ->select(
                'tp.kode_pembelian',
                'tp.tgl_pembelian',
                'tp.kode_supplier',
                'ms.nama_supplier',
                'tp.total_pembelian',
                'tp.catatan_pembelian'
            )
            ->where('tp.kode_pembelian', $kode_pembelian)
            ->first();

        if (!$header) {
            return response()->json([
                'success' => false,
                'message' => 'Data pembelian tidak ditemukan.'
            ], 404);
        }

        $details = DB::table('transaksi_pembelian_detail')
            ->select(
                'kode_barang',
                'nama_barang',
                'qty',
                'harga_barang',
                'subtotal_barang'
            )
            ->where('kode_pembelian', $kode_pembelian)
            ->get();

        return response()->json([
            'success' => true,
            'header'  => $header,
            'details' => $details,
        ]);
    }

    public function updatePembelian(Request $request, $kode_pembelian)
    {
        $request->validate([
            'tgl_pembelian'        => 'required|date',
            'kode_supplier'        => 'required|string',
            'catatan_pembelian'    => 'nullable|string',
            'items'                => 'required|array|min:1',
            'items.*.kode_barang'  => 'required|string',
            'items.*.nama_barang'  => 'required|string',
            'items.*.qty'          => 'required|numeric|min:1',
            'items.*.harga_barang' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();

        if (!$user || empty($user->kode_user)) {
            return response()->json([
                'success' => false,
                'message' => 'Kode user login tidak ditemukan.'
            ], 422);
        }

        try {
            DB::transaction(function () use ($request, $user, $kode_pembelian, &$totalPembelian) {
                $header = DB::table('transaksi_pembelian')
                    ->where('kode_pembelian', $kode_pembelian)
                    ->lockForUpdate()
                    ->first();

                if (!$header) {
                    throw new \Exception('Data pembelian tidak ditemukan.');
                }

                $oldDetails = DB::table('transaksi_pembelian_detail')
                    ->where('kode_pembelian', $kode_pembelian)
                    ->lockForUpdate()
                    ->get();

                $kodeBarangGabungan = collect($request->items)
                    ->pluck('kode_barang')
                    ->merge($oldDetails->pluck('kode_barang'))
                    ->unique()
                    ->values()
                    ->all();

                if (!empty($kodeBarangGabungan)) {
                    DB::table('master_barang')
                        ->whereIn('kode_barang', $kodeBarangGabungan)
                        ->lockForUpdate()
                        ->get();
                }

                foreach ($oldDetails as $old) {
                    $barang = DB::table('master_barang')
                        ->where('kode_barang', $old->kode_barang)
                        ->first();

                    if (!$barang) {
                        throw new \Exception("Barang {$old->kode_barang} tidak ditemukan.");
                    }

                    if ((float) $barang->kapasitas < (float) $old->qty) {
                        throw new \Exception("Stok barang {$old->kode_barang} tidak cukup untuk proses edit.");
                    }

                    DB::table('master_barang')
                        ->where('kode_barang', $old->kode_barang)
                        ->decrement('kapasitas', (float) $old->qty);
                }

                DB::table('transaksi_pembelian_detail')
                    ->where('kode_pembelian', $kode_pembelian)
                    ->delete();

                $detailRows = [];
                $totalPembelian = 0;

                foreach ($request->items as $item) {
                    $qty = (float) $item['qty'];
                    $harga = (float) $item['harga_barang'];
                    $subtotal = $qty * $harga;

                    $totalPembelian += $subtotal;

                    $detailRows[] = [
                        'kode_pembelian'  => $kode_pembelian,
                        'kode_barang'     => $item['kode_barang'],
                        'nama_barang'     => $item['nama_barang'],
                        'qty'             => $qty,
                        'harga_barang'    => $harga,
                        'subtotal_barang' => $subtotal,
                        'date_entry'      => now(),
                    ];
                }

                DB::table('transaksi_pembelian')
                    ->where('kode_pembelian', $kode_pembelian)
                    ->update([
                        'tgl_pembelian'     => $request->tgl_pembelian,
                        'kode_supplier'     => $request->kode_supplier,
                        'kode_user'         => $user->kode_user,
                        'total_pembelian'   => $totalPembelian,
                        'catatan_pembelian' => $request->catatan_pembelian,
                    ]);

                DB::table('transaksi_pembelian_detail')->insert($detailRows);

                foreach ($request->items as $item) {
                    DB::table('master_barang')
                        ->where('kode_barang', $item['kode_barang'])
                        ->increment('kapasitas', (float) $item['qty']);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Transaksi pembelian berhasil diupdate.',
                'kode_pembelian' => $kode_pembelian,
                'total_pembelian' => $totalPembelian,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update transaksi: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function deletePembelian($kode_pembelian)
    {
        try {
            DB::transaction(function () use ($kode_pembelian) {
                $header = DB::table('transaksi_pembelian')
                    ->where('kode_pembelian', $kode_pembelian)
                    ->lockForUpdate()
                    ->first();

                if (!$header) {
                    throw new \Exception('Data pembelian tidak ditemukan.');
                }

                $oldDetails = DB::table('transaksi_pembelian_detail')
                    ->where('kode_pembelian', $kode_pembelian)
                    ->lockForUpdate()
                    ->get();

                $kodeBarangs = $oldDetails->pluck('kode_barang')->unique()->values()->all();

                if (!empty($kodeBarangs)) {
                    DB::table('master_barang')
                        ->whereIn('kode_barang', $kodeBarangs)
                        ->lockForUpdate()
                        ->get();
                }

                foreach ($oldDetails as $old) {
                    $barang = DB::table('master_barang')
                        ->where('kode_barang', $old->kode_barang)
                        ->first();

                    if (!$barang) {
                        throw new \Exception("Barang {$old->kode_barang} tidak ditemukan.");
                    }

                    if ((float) $barang->kapasitas < (float) $old->qty) {
                        throw new \Exception("Stok barang {$old->kode_barang} tidak cukup untuk menghapus transaksi.");
                    }

                    DB::table('master_barang')
                        ->where('kode_barang', $old->kode_barang)
                        ->decrement('kapasitas', (float) $old->qty);
                }

                DB::table('transaksi_pembelian_detail')
                    ->where('kode_pembelian', $kode_pembelian)
                    ->delete();

                DB::table('transaksi_pembelian')
                    ->where('kode_pembelian', $kode_pembelian)
                    ->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Transaksi pembelian berhasil dihapus.'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getCustomerAktifByUser()
    {
        $user = Auth::user();

        if (!$user || empty($user->kode_user)) {
            return null;
        }

        $customer = DB::table('master_customer')
            ->select('kode_customer', 'nama_customer', 'kode_user')
            ->where('kode_user', $user->kode_user)
            ->first();

        if ($customer) {
            return $customer;
        }

        return (object) [
            'kode_customer' => null,
            'nama_customer' => $user->nama_user,
            'kode_user' => $user->kode_user,
        ];
    }

    private function validateOngkirByJenis($jenisPesanan, $ongkirPesanan)
    {
        $ongkir = (float) $ongkirPesanan;

        if ($jenisPesanan === 'Reguler') {
            return $ongkir >= 25000 && $ongkir <= 30000;
        }

        if ($jenisPesanan === 'Express') {
            return $ongkir >= 30000 && $ongkir <= 50000;
        }

        if ($jenisPesanan === 'Preorder') {
            return $ongkir == 50000;
        }

        return false;
    }

    private function isAdminRole($kodeRole)
    {
        return in_array($kodeRole, ['KRL001', 'KRL002']);
    }

    private function isCustomerRole($kodeRole)
    {
        return $kodeRole === 'KRL003';
    }

    private function canEditDeletePenjualan($header)
    {
        return ($header->status_pesanan === 'Pending')
            && ($header->status_pembayaran === 'Belum Dibayar');
    }



    public function uploadBuktiPenjualan(Request $request, $kode_pesanan)
    {
        $user = Auth::user();

        $request->validate([
            'bukti_pembayaran' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $header = $this->getPenjualanHeaderForUser($kode_pesanan, $user);

        if (!$header) {
            return response()->json([
                'success' => false,
                'message' => 'Data penjualan tidak ditemukan atau Anda tidak memiliki akses.'
            ], 404);
        }

        if (!in_array($header->status_pembayaran, ['Belum Dibayar', 'Ditolak'])) {
            return response()->json([
                'success' => false,
                'message' => 'Bukti pembayaran tidak dapat diunggah pada status pembayaran saat ini.'
            ], 422);
        }

        try {
            DB::transaction(function () use ($request, $kode_pesanan, &$path) {
                $path = $request->file('bukti_pembayaran')
                    ->store('bukti-pembayaran', 'public');

                DB::table('pembayaran_penjualan')
                    ->where('kode_pesanan', $kode_pesanan)
                    ->update([
                        'bukti_pembayaran' => $path,
                        'status_pembayaran' => 'Menunggu Validasi',
                        'tanggal_upload_bukti' => now(),
                        'updated_at' => now(),
                    ]);

                DB::table('transaksi_penjualan')
                    ->where('kode_pesanan', $kode_pesanan)
                    ->update([
                        'status_pembayaran' => 'Menunggu Validasi',
                    ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Bukti pembayaran berhasil diunggah dan menunggu validasi admin.',
                'bukti_pembayaran' => $path,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah bukti pembayaran: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function validasiPembayaranPenjualan($kode_pesanan)
    {
        $user = Auth::user();

        if (!$this->isAdminRole($user->kode_role)) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya admin atau karyawan yang dapat memvalidasi pembayaran.'
            ], 403);
        }

        $transaksi = DB::table('transaksi_penjualan')
            ->where('kode_pesanan', $kode_pesanan)
            ->first();

        if (!$transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi penjualan tidak ditemukan.'
            ], 404);
        }

        if ($transaksi->status_pembayaran !== 'Menunggu Validasi') {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran hanya dapat divalidasi jika statusnya Menunggu Validasi.'
            ], 422);
        }

        DB::transaction(function () use ($kode_pesanan, $user) {
            DB::table('pembayaran_penjualan')
                ->where('kode_pesanan', $kode_pesanan)
                ->update([
                    'status_pembayaran' => 'Lunas',
                    'tanggal_validasi' => now(),
                    'divalidasi_oleh' => $user->kode_user,
                    'catatan_validasi' => null,
                    'updated_at' => now(),
                ]);

            DB::table('transaksi_penjualan')
                ->where('kode_pesanan', $kode_pesanan)
                ->update([
                    'status_pembayaran' => 'Lunas',
                    'status_pesanan' => 'Diproses',
                ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil divalidasi. Status pesanan berubah menjadi Diproses.'
        ]);
    }

    public function tolakPembayaranPenjualan(Request $request, $kode_pesanan)
    {
        $user = Auth::user();

        if (!$this->isAdminRole($user->kode_role)) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya admin atau karyawan yang dapat menolak pembayaran.'
            ], 403);
        }

        $request->validate([
            'catatan_validasi' => 'nullable|string|max:255',
        ]);

        $transaksi = DB::table('transaksi_penjualan')
            ->where('kode_pesanan', $kode_pesanan)
            ->first();

        if (!$transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi penjualan tidak ditemukan.'
            ], 404);
        }

        if ($transaksi->status_pembayaran !== 'Menunggu Validasi') {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran hanya dapat ditolak jika statusnya Menunggu Validasi.'
            ], 422);
        }

        DB::transaction(function () use ($request, $kode_pesanan, $user) {
            DB::table('pembayaran_penjualan')
                ->where('kode_pesanan', $kode_pesanan)
                ->update([
                    'status_pembayaran' => 'Ditolak',
                    'tanggal_validasi' => now(),
                    'divalidasi_oleh' => $user->kode_user,
                    'catatan_validasi' => $request->catatan_validasi,
                    'updated_at' => now(),
                ]);

            DB::table('transaksi_penjualan')
                ->where('kode_pesanan', $kode_pesanan)
                ->update([
                    'status_pembayaran' => 'Ditolak',
                ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran ditolak. Customer dapat mengunggah ulang bukti pembayaran.'
        ]);
    }

    public function updateStatusPesananPenjualan(Request $request, $kode_pesanan)
    {
        $user = Auth::user();

        if (!$this->isAdminRole($user->kode_role)) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya admin atau karyawan yang dapat mengubah status pesanan.'
            ], 403);
        }

        $request->validate([
            'status_pesanan' => 'required|string|in:Pending,Diproses,Dikirim,Selesai,Batal',
        ]);

        $transaksi = DB::table('transaksi_penjualan')
            ->where('kode_pesanan', $kode_pesanan)
            ->first();

        if (!$transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi penjualan tidak ditemukan.'
            ], 404);
        }

        if ($request->status_pesanan !== 'Batal' && $transaksi->status_pembayaran !== 'Lunas') {
            return response()->json([
                'success' => false,
                'message' => 'Status pesanan hanya dapat diproses jika pembayaran sudah lunas.'
            ], 422);
        }

        DB::table('transaksi_penjualan')
            ->where('kode_pesanan', $kode_pesanan)
            ->update([
                'status_pesanan' => $request->status_pesanan,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Status pesanan berhasil diperbarui.'
        ]);
    }

    private function getPenjualanHeaderForUser($kodePesanan, $user)
    {
        $query = DB::table('transaksi_penjualan as tp')
            ->leftJoin('master_customer as mc', 'tp.kode_customer', '=', 'mc.kode_customer')
            ->select(
                'tp.kode_pesanan',
                'tp.tgl_pesanan',
                'tp.kode_customer',
                'mc.nama_customer',
                'tp.jenis_pengiriman',
                'tp.jenis_pemesanan',
                'tp.status_pesanan',
                'tp.status_pembayaran',
                'tp.alamat_kirim_pesanan',
                'tp.ongkir_pesanan',
                'tp.total_detail_pesanan',
                'tp.grand_total_pesanan',
                'tp.catatan_pesanan',
                'tp.spesifikasi_tambahan',
                'tp.harga_estimasi',
                'tp.status_custom'
            )
            ->where('tp.kode_pesanan', $kodePesanan);

        if ($this->isCustomerRole($user->kode_role)) {
            $customerAktif = $this->getCustomerAktifByUser();

            if (!$customerAktif || empty($customerAktif->kode_customer)) {
                return null;
            }

            $query->where('tp.kode_customer', $customerAktif->kode_customer);
        }

        return $query->first();
    }

    public function penjualan()
    {
        $user = Auth::user();
        $customerAktif = $this->getCustomerAktifByUser();

        $barangs = DB::table('master_barang as mb')
            ->leftJoin('master_kategori_barang as mk', 'mb.kode_kategori', '=', 'mk.kode_kategori')
            ->select(
                'mb.kode_barang',
                'mb.nama_barang',
                'mb.harga_jual',
                'mb.kapasitas',
                'mb.kode_kategori',
                'mk.nama_kategori'
            )
            ->orderBy('mk.nama_kategori')
            ->orderBy('mb.nama_barang')
            ->get();

        $kategoris = DB::table('master_kategori_barang')
            ->select('kode_kategori', 'nama_kategori')
            ->orderBy('nama_kategori')
            ->get();

        $customers = collect();

        if ($this->isAdminRole($user->kode_role)) {
            $customers = DB::table('master_customer')
                ->select('kode_customer', 'nama_customer')
                ->orderBy('nama_customer')
                ->get();
        }

        $rekeningPembayaran = DB::table('master_rekening_pembayaran')
        ->where('status_aktif', 1)
        ->orderBy('metode_pembayaran')
        ->orderBy('nama_bank')
        ->get();

        return view('transaksi.transaksi_penjualan', [
            'kodePreview'   => $this->previewKodePesanan(),
            'customerAktif' => $customerAktif,
            'customers'     => $customers,
            'barangs'       => $barangs,
            'kategoris'     => $kategoris,
            'user'          => $user,
            'rekeningPembayaran' => $rekeningPembayaran,
        ]);
    }

    public function storePenjualan(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'tgl_pesanan'          => 'required|date',
            'jenis_pengiriman' => 'required|string|max:50',
            'provinsi_tujuan' => 'nullable|string|max:100',
            'kota_tujuan' => 'nullable|string|max:100',
            'kurir' => 'required|string|max:50',
            'layanan_kurir' => 'required|string|max:100',
            'estimasi_pengiriman' => 'nullable|string|max:50',
            'jenis_pemesanan'      => 'nullable|string|in:Standart,Custom',
            'alamat_kirim_pesanan' => 'nullable|string',
            'ongkir_pesanan'       => 'required|numeric|min:0',
            'catatan_pesanan'      => 'nullable|string',
            'items'                => 'required|array|min:1',
            'items.*.kode_barang'  => 'required|string',
            'items.*.nama_barang'  => 'required|string',
            'items.*.qty'          => 'required|numeric|min:1',
            'metode_pembayaran' => 'required|string|in:Transfer Bank,QRIS,Cash',
            'bank_tujuan' => 'nullable|string|max:50',
        ];

        if ($this->isAdminRole($user->kode_role)) {
            $rules['kode_customer'] = 'required|string|exists:master_customer,kode_customer';
            $rules['status_pesanan'] = 'required|string';
        }

        $request->validate($rules);


        $jenisPemesanan = $request->jenis_pemesanan ?: 'Standart';

        if ($request->metode_pembayaran === 'Transfer Bank' && empty($request->bank_tujuan)) {
            return response()->json([
                'success' => false,
                'message' => 'Bank tujuan wajib dipilih untuk metode Transfer Bank.'
            ], 422);
        }

        if ($this->isCustomerRole($user->kode_role)) {
            $customerAktif = $this->getCustomerAktifByUser();

            if (!$customerAktif || empty($customerAktif->kode_customer)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer untuk user login tidak ditemukan.'
                ], 422);
            }

            $kodeCustomer = $customerAktif->kode_customer;
            $statusPesanan = 'Pending';
        } else {
            $kodeCustomer = $request->kode_customer;
            $statusPesanan = $request->status_pesanan ?: 'Pending';
        }

        try {
            DB::transaction(function () use (
                $request,
                $user,
                $kodeCustomer,
                $statusPesanan,
                $jenisPemesanan,
                &$kodePesanan,
                &$totalDetail,
                &$grandTotal
            ) {
                $kodePesanan = $this->generateKodePesananLocked();
                $totalDetail = 0;

                $kodeBarangs = collect($request->items)
                    ->pluck('kode_barang')
                    ->unique()
                    ->values()
                    ->all();

                if (!empty($kodeBarangs)) {
                    DB::table('master_barang')
                        ->whereIn('kode_barang', $kodeBarangs)
                        ->lockForUpdate()
                        ->get();
                }

                $detailRows = [];

                foreach ($request->items as $item) {
                    $qty = (float) $item['qty'];

                    $barang = DB::table('master_barang')
                        ->select('kode_barang', 'nama_barang', 'kapasitas', 'harga_jual')
                        ->where('kode_barang', $item['kode_barang'])
                        ->first();

                    if (!$barang) {
                        throw new \Exception("Barang {$item['kode_barang']} tidak ditemukan.");
                    }

                    if ((float) $barang->kapasitas <= 0) {
                        throw new \Exception("Barang {$item['kode_barang']} stok tidak tersedia.");
                    }

                    if ((float) $barang->kapasitas < $qty) {
                        throw new \Exception("Stok barang {$item['kode_barang']} tidak mencukupi.");
                    }

                    $hargaSatuan = (float) $barang->harga_jual;
                    $subtotal = $qty * $hargaSatuan;

                    DB::table('master_barang')
                        ->where('kode_barang', $item['kode_barang'])
                        ->decrement('kapasitas', $qty);

                    $totalDetail += $subtotal;

                    $detailRows[] = [
                        'kode_pesanan'     => $kodePesanan,
                        'kode_barang'      => $barang->kode_barang,
                        'nama_barang'      => $barang->nama_barang,
                        'qty'              => $qty,
                        'harga_satuan'     => $hargaSatuan,
                        'subtotal_pesanan' => $subtotal,
                        'date_entry'       => now(),
                    ];
                }

                $grandTotal = $totalDetail + (float) $request->ongkir_pesanan;

                DB::table('transaksi_penjualan')->insert([
                    'kode_pesanan'         => $kodePesanan,
                    'tgl_pesanan'          => $request->tgl_pesanan,
                    'kode_customer'        => $kodeCustomer,
                    'jenis_pengiriman'     => $request->jenis_pengiriman,
                    'jenis_pemesanan'      => $jenisPemesanan,
                    'status_pesanan'       => $statusPesanan,
                    'alamat_kirim_pesanan' => $request->alamat_kirim_pesanan,
                    'ongkir_pesanan'       => (float) $request->ongkir_pesanan,
                    'catatan_pesanan'      => $request->catatan_pesanan,
                    'spesifikasi_tambahan' => null,
                    'harga_estimasi'       => null,
                    'status_custom'        => null,
                    'total_detail_pesanan' => $totalDetail,
                    'grand_total_pesanan'  => $grandTotal,
                    'status_pembayaran'    => 'Belum Dibayar',
                    'provinsi_tujuan' => $request->provinsi_tujuan,
                    'kota_tujuan' => $request->kota_tujuan,
                    'kurir' => $request->kurir,
                    'layanan_kurir' => $request->layanan_kurir,
                    'estimasi_pengiriman' => $request->estimasi_pengiriman,
                ]);

                DB::table('pembayaran_penjualan')->insert([
                    'kode_pembayaran' => $this->generateKodePembayaran(),
                    'kode_pesanan' => $kodePesanan,
                    'metode_pembayaran' => $request->metode_pembayaran,
                    'bank_tujuan' => $request->bank_tujuan,
                    'nominal_pembayaran' => $grandTotal,
                    'status_pembayaran' => 'Belum Dibayar',
                    'created_at' => now(),
                ]);

                DB::table('transaksi_penjualan_detail')->insert($detailRows);

            });

            return response()->json([
                'success'              => true,
                'message'              => 'Transaksi penjualan berhasil disimpan.',
                'kode_pesanan'         => $kodePesanan,
                'total_detail_pesanan' => $totalDetail,
                'grand_total_pesanan'  => $grandTotal,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan transaksi: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function generateKodePembayaran()
    {
        $lastKode = DB::table('pembayaran_penjualan')
            ->orderByDesc('kode_pembayaran')
            ->value('kode_pembayaran');

        if (!$lastKode) {
            return 'PAY001';
        }

        $angka = (int) substr($lastKode, 3);

        return 'PAY' . str_pad($angka + 1, 3, '0', STR_PAD_LEFT);
    }

    public function listPenjualan()
    {
        $user = Auth::user();

        $query = DB::table('transaksi_penjualan as tp')
            ->leftJoin('master_customer as mc', 'tp.kode_customer', '=', 'mc.kode_customer')
            ->leftJoin('pembayaran_penjualan as pp', 'tp.kode_pesanan', '=', 'pp.kode_pesanan')
            ->select(
                'tp.kode_pesanan',
                'tp.tgl_pesanan',
                'tp.kode_customer',
                'mc.nama_customer',
                'tp.jenis_pengiriman',
                'tp.status_pesanan',
                'tp.status_pembayaran',
                'tp.grand_total_pesanan',
                'pp.metode_pembayaran',
                'pp.bank_tujuan',
                'pp.bukti_pembayaran'
            );

        if ($this->isCustomerRole($user->kode_role)) {
            $customerAktif = $this->getCustomerAktifByUser();

            if (!$customerAktif || empty($customerAktif->kode_customer)) {
                return response()->json(['data' => []]);
            }

            $query->where('tp.kode_customer', $customerAktif->kode_customer);
        }

        $data = $query
            ->orderByDesc('tp.tgl_pesanan')
            ->orderByDesc('tp.kode_pesanan')
            ->get();

        return response()->json(['data' => $data]);
    }

    public function showPenjualan($kode_pesanan)
    {
        $user = Auth::user();

        $header = $this->getPenjualanHeaderForUser($kode_pesanan, $user);

        if (!$header) {
            return response()->json([
                'success' => false,
                'message' => 'Data penjualan tidak ditemukan atau Anda tidak memiliki akses.'
            ], 404);
        }

        $details = DB::table('transaksi_penjualan_detail')
            ->select(
                'kode_barang',
                'nama_barang',
                'qty',
                'harga_satuan',
                'subtotal_pesanan'
            )
            ->where('kode_pesanan', $kode_pesanan)
            ->get();

            return response()->json([
                'success'  => true,
                'header'   => $header,
                'details'  => $details,
                'can_edit' => $this->isAdminRole($user->kode_role) || $this->canEditDeletePenjualan($header),
            ]);
    }

    public function updatePenjualan(Request $request, $kode_pesanan)
    {
        $user = Auth::user();

        $rules = [
            'tgl_pesanan'          => 'required|date',
            'jenis_pengiriman' => 'required|string|max:50',
            'provinsi_tujuan' => 'nullable|string|max:100',
            'kota_tujuan' => 'nullable|string|max:100',
            'kurir' => 'required|string|max:50',
            'layanan_kurir' => 'required|string|max:100',
            'estimasi_pengiriman' => 'nullable|string|max:50',
            'jenis_pemesanan'      => 'nullable|string|in:Standart,Custom',
            'alamat_kirim_pesanan' => 'nullable|string',
            'ongkir_pesanan'       => 'required|numeric|min:0',
            'catatan_pesanan'      => 'nullable|string',
            'items'                => 'required|array|min:1',
            'items.*.kode_barang'  => 'required|string',
            'items.*.nama_barang'  => 'required|string',
            'items.*.qty'          => 'required|numeric|min:1',
        ];

        if ($this->isAdminRole($user->kode_role)) {
            $rules['kode_customer'] = 'required|string|exists:master_customer,kode_customer';
            $rules['status_pesanan'] = 'required|string';
        }

        $request->validate($rules);

        if (!$this->validateOngkirByJenis($request->jenis_pengiriman, $request->ongkir_pesanan)) {
            return response()->json([
                'success' => false,
                'message' => 'Nilai ongkir tidak sesuai dengan jenis pengiriman.'
            ], 422);
        }

        $jenisPemesanan = $request->jenis_pemesanan ?: 'Standart';

        $headerAkses = $this->getPenjualanHeaderForUser($kode_pesanan, $user);

        if (!$headerAkses) {
            return response()->json([
                'success' => false,
                'message' => 'Data penjualan tidak ditemukan atau Anda tidak memiliki akses.'
            ], 404);
        }

        if (!$this->isAdminRole($user->kode_role) && !$this->canEditDeletePenjualan($headerAkses)) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi sudah terkunci karena pembayaran sudah diproses atau pesanan tidak lagi pending.'
            ], 403);
        }

        if ($this->isCustomerRole($user->kode_role)) {
            $customerAktif = $this->getCustomerAktifByUser();

            if (!$customerAktif || empty($customerAktif->kode_customer)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer untuk user login tidak ditemukan.'
                ], 422);
            }

            $kodeCustomer = $customerAktif->kode_customer;
            $statusPesanan = $headerAkses->status_pesanan;
        } else {
            $kodeCustomer = $request->kode_customer;
            $statusPesanan = $request->status_pesanan ?: $headerAkses->status_pesanan;
        }

        try {
            DB::transaction(function () use (
                $request,
                $kode_pesanan,
                $kodeCustomer,
                $statusPesanan,
                $jenisPemesanan,
                &$totalDetail,
                &$grandTotal
            ) {
                $header = DB::table('transaksi_penjualan')
                    ->where('kode_pesanan', $kode_pesanan)
                    ->lockForUpdate()
                    ->first();

                if (!$header) {
                    throw new \Exception('Data penjualan tidak ditemukan.');
                }

                $oldDetails = DB::table('transaksi_penjualan_detail')
                    ->where('kode_pesanan', $kode_pesanan)
                    ->lockForUpdate()
                    ->get();

                $kodeBarangGabungan = collect($request->items)
                    ->pluck('kode_barang')
                    ->merge($oldDetails->pluck('kode_barang'))
                    ->unique()
                    ->values()
                    ->all();

                if (!empty($kodeBarangGabungan)) {
                    DB::table('master_barang')
                        ->whereIn('kode_barang', $kodeBarangGabungan)
                        ->lockForUpdate()
                        ->get();
                }

                foreach ($oldDetails as $old) {
                    DB::table('master_barang')
                        ->where('kode_barang', $old->kode_barang)
                        ->increment('kapasitas', (float) $old->qty);
                }

                DB::table('transaksi_penjualan_detail')
                    ->where('kode_pesanan', $kode_pesanan)
                    ->delete();

                $detailRows = [];
                $totalDetail = 0;

                foreach ($request->items as $item) {
                    $qty = (float) $item['qty'];

                    $barang = DB::table('master_barang')
                        ->select('kode_barang', 'nama_barang', 'kapasitas', 'harga_jual')
                        ->where('kode_barang', $item['kode_barang'])
                        ->first();

                    if (!$barang) {
                        throw new \Exception("Barang {$item['kode_barang']} tidak ditemukan.");
                    }

                    if ((float) $barang->kapasitas <= 0) {
                        throw new \Exception("Barang {$item['kode_barang']} stok tidak tersedia.");
                    }

                    if ((float) $barang->kapasitas < $qty) {
                        throw new \Exception("Stok barang {$item['kode_barang']} tidak mencukupi untuk proses update.");
                    }

                    $hargaSatuan = (float) $barang->harga_jual;
                    $subtotal = $qty * $hargaSatuan;

                    DB::table('master_barang')
                        ->where('kode_barang', $item['kode_barang'])
                        ->decrement('kapasitas', $qty);

                    $totalDetail += $subtotal;

                    $detailRows[] = [
                        'kode_pesanan'     => $kode_pesanan,
                        'kode_barang'      => $barang->kode_barang,
                        'nama_barang'      => $barang->nama_barang,
                        'qty'              => $qty,
                        'harga_satuan'     => $hargaSatuan,
                        'subtotal_pesanan' => $subtotal,
                        'date_entry'       => now(),
                    ];
                }

                $grandTotal = $totalDetail + (float) $request->ongkir_pesanan;

                DB::table('transaksi_penjualan')
                    ->where('kode_pesanan', $kode_pesanan)
                    ->update([
                        'tgl_pesanan'          => $request->tgl_pesanan,
                        'kode_customer'        => $kodeCustomer,
                        'jenis_pengiriman'     => $request->jenis_pengiriman,
                        'jenis_pemesanan'      => $jenisPemesanan,
                        'status_pesanan'       => $statusPesanan,
                        'alamat_kirim_pesanan' => $request->alamat_kirim_pesanan,
                        'ongkir_pesanan'       => (float) $request->ongkir_pesanan,
                        'catatan_pesanan'      => $request->catatan_pesanan,
                        'spesifikasi_tambahan' => null,
                        'harga_estimasi'       => null,
                        'status_custom'        => null,
                        'total_detail_pesanan' => $totalDetail,
                        'grand_total_pesanan'  => $grandTotal,
                        'provinsi_tujuan' => $request->provinsi_tujuan,
                        'kota_tujuan' => $request->kota_tujuan,
                        'kurir' => $request->kurir,
                        'layanan_kurir' => $request->layanan_kurir,
                        'estimasi_pengiriman' => $request->estimasi_pengiriman,
                    ]);

                DB::table('transaksi_penjualan_detail')->insert($detailRows);

            });

            return response()->json([
                'success'              => true,
                'message'              => 'Transaksi penjualan berhasil diupdate.',
                'kode_pesanan'         => $kode_pesanan,
                'total_detail_pesanan' => $totalDetail,
                'grand_total_pesanan'  => $grandTotal,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update transaksi: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function deletePenjualan($kode_pesanan)
    {
        $user = Auth::user();

        $headerAkses = $this->getPenjualanHeaderForUser($kode_pesanan, $user);

        if (!$headerAkses) {
            return response()->json([
                'success' => false,
                'message' => 'Data penjualan tidak ditemukan atau Anda tidak memiliki akses.'
            ], 404);
        }

        if (!$this->isAdminRole($user->kode_role) && !$this->canEditDeletePenjualan($headerAkses)) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi sudah terkunci dan tidak dapat dihapus.'
            ], 403);
        }

        try {
            DB::transaction(function () use ($kode_pesanan) {
                $header = DB::table('transaksi_penjualan')
                    ->where('kode_pesanan', $kode_pesanan)
                    ->lockForUpdate()
                    ->first();

                if (!$header) {
                    throw new \Exception('Data penjualan tidak ditemukan.');
                }

                $oldDetails = DB::table('transaksi_penjualan_detail')
                    ->where('kode_pesanan', $kode_pesanan)
                    ->lockForUpdate()
                    ->get();

                $kodeBarangs = $oldDetails->pluck('kode_barang')->unique()->values()->all();

                if (!empty($kodeBarangs)) {
                    DB::table('master_barang')
                        ->whereIn('kode_barang', $kodeBarangs)
                        ->lockForUpdate()
                        ->get();
                }

                foreach ($oldDetails as $old) {
                    DB::table('master_barang')
                        ->where('kode_barang', $old->kode_barang)
                        ->increment('kapasitas', (float) $old->qty);
                }

                DB::table('transaksi_penjualan_detail')
                    ->where('kode_pesanan', $kode_pesanan)
                    ->delete();

                DB::table('pembayaran_penjualan')
                    ->where('kode_pesanan', $kode_pesanan)
                    ->delete();

                DB::table('transaksi_penjualan')
                    ->where('kode_pesanan', $kode_pesanan)
                    ->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Transaksi penjualan berhasil dihapus.'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function approveCustomPenjualan($kode_pesanan)
    {
        $user = Auth::user();

        if (!$this->isCustomerRole($user->kode_role)) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya customer yang dapat melanjutkan pesanan custom.'
            ], 403);
        }

        $header = $this->getPenjualanHeaderForUser($kode_pesanan, $user);

        if (!$header) {
            return response()->json([
                'success' => false,
                'message' => 'Data penjualan tidak ditemukan atau Anda tidak memiliki akses.'
            ], 404);
        }

        if ($header->jenis_pemesanan !== 'Custom') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan ini bukan pesanan custom.'
            ], 422);
        }

        if (empty($header->harga_estimasi) || (float) $header->harga_estimasi <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Harga estimasi belum diisi admin.'
            ], 422);
        }

        DB::table('transaksi_penjualan')
            ->where('kode_pesanan', $kode_pesanan)
            ->update([
                'status_custom' => 'lanjutkan'
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Pesanan custom berhasil dilanjutkan.'
        ]);
    }

    public function rejectCustomPenjualan($kode_pesanan)
    {
        $user = Auth::user();

        if (!$this->isCustomerRole($user->kode_role)) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya customer yang dapat menolak pesanan custom.'
            ], 403);
        }

        $header = $this->getPenjualanHeaderForUser($kode_pesanan, $user);

        if (!$header) {
            return response()->json([
                'success' => false,
                'message' => 'Data penjualan tidak ditemukan atau Anda tidak memiliki akses.'
            ], 404);
        }

        if ($header->jenis_pemesanan !== 'Custom') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan ini bukan pesanan custom.'
            ], 422);
        }

        DB::table('transaksi_penjualan')
            ->where('kode_pesanan', $kode_pesanan)
            ->update([
                'status_custom' => 'batal'
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Pesanan custom tidak dilanjutkan, tetapi tetap tercatat sebagai custom.'
        ]);
    }

    private function previewKodePesanan()
    {
        $lastKode = DB::table('transaksi_penjualan')
            ->orderByDesc('kode_pesanan')
            ->value('kode_pesanan');

        if (!$lastKode) {
            return 'KPS001';
        }

        $angka = (int) substr($lastKode, 3);
        return 'KPS' . str_pad($angka + 1, 3, '0', STR_PAD_LEFT);
    }

    private function generateKodePesananLocked()
    {
        $lastKode = DB::table('transaksi_penjualan')
            ->lockForUpdate()
            ->orderByDesc('kode_pesanan')
            ->value('kode_pesanan');

        if (!$lastKode) {
            return 'KPS001';
        }

        $angka = (int) substr($lastKode, 3);
        return 'KPS' . str_pad($angka + 1, 3, '0', STR_PAD_LEFT);
    }

    // RETUR PEMBELIAN

    public function returPembelian()
    {
        $pembelians = DB::table('transaksi_pembelian as tp')
            ->leftJoin('master_supplier as ms', 'tp.kode_supplier', '=', 'ms.kode_supplier')
            ->select(
                'tp.kode_pembelian',
                'tp.tgl_pembelian',
                'tp.kode_supplier',
                'ms.nama_supplier'
            )
            ->orderByDesc('tp.tgl_pembelian')
            ->orderByDesc('tp.kode_pembelian')
            ->get();

        return view('transaksi.retur_pembelian', [
            'kodePreview' => $this->previewKodeReturPembelian(),
            'pembelians'  => $pembelians,
        ]);
    }

    private function previewKodeReturPembelian()
    {
        $lastKode = DB::table('retur_pembelian')
            ->orderByDesc('kode_rpembelian')
            ->value('kode_rpembelian');

        if (!$lastKode) {
            return 'KRP001';
        }

        $angka = (int) substr($lastKode, 3);

        return 'KRP' . str_pad($angka + 1, 3, '0', STR_PAD_LEFT);
    }

    private function generateKodeReturPembelianLocked()
    {
        $lastKode = DB::table('retur_pembelian')
            ->lockForUpdate()
            ->orderByDesc('kode_rpembelian')
            ->value('kode_rpembelian');

        if (!$lastKode) {
            return 'KRP001';
        }

        $angka = (int) substr($lastKode, 3);

        return 'KRP' . str_pad($angka + 1, 3, '0', STR_PAD_LEFT);
    }

    public function getPembelianUntukRetur($kode_pembelian)
    {
        $header = DB::table('transaksi_pembelian as tp')
            ->leftJoin('master_supplier as ms', 'tp.kode_supplier', '=', 'ms.kode_supplier')
            ->select(
                'tp.kode_pembelian',
                'tp.tgl_pembelian',
                'tp.kode_supplier',
                'ms.nama_supplier',
                'ms.alamat_supplier as alamat'
            )
            ->where('tp.kode_pembelian', $kode_pembelian)
            ->first();

        if (!$header) {
            return response()->json([
                'success' => false,
                'message' => 'Data pembelian tidak ditemukan.'
            ], 404);
        }

        $details = DB::table('transaksi_pembelian_detail as tpd')
            ->leftJoin('retur_pembelian_detail as rpd', function ($join) {
                $join->on('tpd.kode_pembelian', '=', 'rpd.kode_pembelian')
                    ->on('tpd.kode_barang', '=', 'rpd.kode_barang');
            })
            ->select(
                'tpd.kode_pembelian',
                'tpd.kode_barang',
                'tpd.nama_barang',
                'tpd.qty',
                'tpd.harga_barang',
                'tpd.subtotal_barang',
                DB::raw('COALESCE(SUM(rpd.qty_retur), 0) as qty_sudah_retur'),
                DB::raw('(tpd.qty - COALESCE(SUM(rpd.qty_retur), 0)) as sisa_retur')
            )
            ->where('tpd.kode_pembelian', $kode_pembelian)
            ->groupBy(
                'tpd.kode_pembelian',
                'tpd.kode_barang',
                'tpd.nama_barang',
                'tpd.qty',
                'tpd.harga_barang',
                'tpd.subtotal_barang'
            )
            ->get();

        return response()->json([
            'success' => true,
            'header'  => $header,
            'details' => $details,
        ]);
    }

    public function storeReturPembelian(Request $request)
    {
        $request->validate([
            'tgl_pembelian'       => 'required|date',
            'kode_supplier'       => 'required|string',
            'alamat'              => 'required|string|max:50',
            'kode_pembelian'      => 'required|string|exists:transaksi_pembelian,kode_pembelian',
            'keterangan'          => 'nullable|string|max:250',
            'items'               => 'required|array|min:1',
            'items.*.kode_barang' => 'required|string',
            'items.*.nama_barang' => 'required|string',
            'items.*.qty_retur'   => 'required|numeric|min:1',
        ]);

        try {
            DB::transaction(function () use ($request, &$kodeRetur) {
                $kodeRetur = $this->generateKodeReturPembelianLocked();

                $pembelian = DB::table('transaksi_pembelian')
                    ->where('kode_pembelian', $request->kode_pembelian)
                    ->lockForUpdate()
                    ->first();

                if (!$pembelian) {
                    throw new \Exception('Data pembelian tidak ditemukan.');
                }

                if ($pembelian->kode_supplier !== $request->kode_supplier) {
                    throw new \Exception('Supplier tidak sesuai dengan data pembelian.');
                }

                DB::table('retur_pembelian')->insert([
                    'kode_rpembelian' => $kodeRetur,
                    'tgl_pembelian'   => $request->tgl_pembelian,
                    'kode_supplier'   => $request->kode_supplier,
                    'alamat'          => $request->alamat,
                    'kode_pembelian'  => $request->kode_pembelian,
                    'keterangan'      => $request->keterangan,
                ]);

                $detailRows = [];

                foreach ($request->items as $item) {
                    $detailPembelian = DB::table('transaksi_pembelian_detail')
                        ->where('kode_pembelian', $request->kode_pembelian)
                        ->where('kode_barang', $item['kode_barang'])
                        ->lockForUpdate()
                        ->first();

                    if (!$detailPembelian) {
                        throw new \Exception("Barang {$item['kode_barang']} tidak ditemukan pada transaksi pembelian {$request->kode_pembelian}.");
                    }

                    $qtyRetur = (float) $item['qty_retur'];
                    $qtyBeli = (float) $detailPembelian->qty;

                    $qtySudahDiretur = DB::table('retur_pembelian_detail')
                        ->where('kode_pembelian', $request->kode_pembelian)
                        ->where('kode_barang', $item['kode_barang'])
                        ->sum('qty_retur');

                    $sisaBisaDiretur = $qtyBeli - (float) $qtySudahDiretur;

                    if ($qtyRetur > $sisaBisaDiretur) {
                        throw new \Exception("Qty retur barang {$item['kode_barang']} melebihi sisa qty yang dapat diretur. Sisa retur: {$sisaBisaDiretur}.");
                    }

                    $barang = DB::table('master_barang')
                        ->where('kode_barang', $item['kode_barang'])
                        ->lockForUpdate()
                        ->first();

                    if (!$barang) {
                        throw new \Exception("Barang {$item['kode_barang']} tidak ditemukan di master barang.");
                    }

                    if ((float) $barang->kapasitas < $qtyRetur) {
                        throw new \Exception("Stok barang {$item['kode_barang']} tidak cukup untuk retur pembelian.");
                    }

                    $hargaBarang = (float) $detailPembelian->harga_barang;
                    $subtotalRetur = $qtyRetur * $hargaBarang;

                    $detailRows[] = [
                        'kode_rpembelian' => $kodeRetur,
                        'kode_pembelian'  => $request->kode_pembelian,
                        'kode_barang'     => $detailPembelian->kode_barang,
                        'nama_barang'     => $detailPembelian->nama_barang,
                        'qty_beli'        => $qtyBeli,
                        'qty_retur'       => $qtyRetur,
                        'harga_barang'    => $hargaBarang,
                        'subtotal_retur'  => $subtotalRetur,
                        'date_entry'      => now(),
                    ];

                    /*
                        Jangan decrement stok di sini jika sudah memakai trigger.

                        Trigger retur_pembelian_detail AFTER INSERT akan menjalankan:
                        UPDATE master_barang
                        SET kapasitas = kapasitas - NEW.qty_retur
                        WHERE kode_barang = NEW.kode_barang;
                    */
                }

                DB::table('retur_pembelian_detail')->insert($detailRows);
            });

            return response()->json([
                'success'          => true,
                'message'          => 'Retur pembelian berhasil disimpan.',
                'kode_rpembelian'  => $kodeRetur,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan retur pembelian: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function listReturPembelian()
    {
        $data = DB::table('retur_pembelian as rp')
            ->leftJoin('master_supplier as ms', 'rp.kode_supplier', '=', 'ms.kode_supplier')
            ->select(
                'rp.kode_rpembelian',
                'rp.tgl_pembelian',
                'rp.kode_pembelian',
                'rp.kode_supplier',
                'ms.nama_supplier',
                'rp.alamat',
                'rp.keterangan'
            )
            ->orderByDesc('rp.tgl_pembelian')
            ->orderByDesc('rp.kode_rpembelian')
            ->get();

        return response()->json(['data' => $data]);
    }

    // RETUR PENJUALAN

    private function previewKodeReturPenjualan()
    {
        $lastKode = DB::table('retur_penjualan')
            ->orderByDesc('kode_rpenjualan')
            ->value('kode_rpenjualan');

        if (!$lastKode) {
            return 'KRJ001';
        }

        $angka = (int) substr($lastKode, 3);

        return 'KRJ' . str_pad($angka + 1, 3, '0', STR_PAD_LEFT);
    }

    private function generateKodeReturPenjualanLocked()
    {
        $lastKode = DB::table('retur_penjualan')
            ->lockForUpdate()
            ->orderByDesc('kode_rpenjualan')
            ->value('kode_rpenjualan');

        if (!$lastKode) {
            return 'KRJ001';
        }

        $angka = (int) substr($lastKode, 3);

        return 'KRJ' . str_pad($angka + 1, 3, '0', STR_PAD_LEFT);
    }

    public function getPenjualanUntukRetur($kode_pesanan)
    {
        $user = Auth::user();

        $query = DB::table('transaksi_penjualan as tp')
            ->leftJoin('master_customer as mc', 'tp.kode_customer', '=', 'mc.kode_customer')
            ->select(
                'tp.kode_pesanan',
                'tp.tgl_pesanan',
                'tp.kode_customer',
                'mc.nama_customer',
                'mc.alamat_customer as alamat'
            )
            ->where('tp.kode_pesanan', $kode_pesanan)
            ->where('tp.status_pesanan', 'Selesai');;

        if ($this->isCustomerRole($user->kode_role)) {
            $customerAktif = $this->getCustomerAktifByUser();

            if (!$customerAktif || empty($customerAktif->kode_customer)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer untuk user login tidak ditemukan.'
                ], 422);
            }

            $query->where('tp.kode_customer', $customerAktif->kode_customer);
        }

        $header = $query->first();

        if (!$header) {
            return response()->json([
                'success' => false,
                'message' => 'Data penjualan tidak ditemukan atau Anda tidak memiliki akses.'
            ], 404);
        }

        $details = DB::table('transaksi_penjualan_detail as tpd')
            ->leftJoin('retur_penjualan_detail as rpd', function ($join) {
                $join->on('tpd.kode_pesanan', '=', 'rpd.kode_pesanan')
                    ->on('tpd.kode_barang', '=', 'rpd.kode_barang');
            })
            ->select(
                'tpd.kode_pesanan',
                'tpd.kode_barang',
                'tpd.nama_barang',
                'tpd.qty',
                'tpd.harga_satuan',
                'tpd.subtotal_pesanan',
                DB::raw('COALESCE(SUM(rpd.qty_retur), 0) as qty_sudah_retur'),
                DB::raw('(tpd.qty - COALESCE(SUM(rpd.qty_retur), 0)) as sisa_retur')
            )
            ->where('tpd.kode_pesanan', $kode_pesanan)
            ->groupBy(
                'tpd.kode_pesanan',
                'tpd.kode_barang',
                'tpd.nama_barang',
                'tpd.qty',
                'tpd.harga_satuan',
                'tpd.subtotal_pesanan'
            )
            ->get();

        return response()->json([
            'success' => true,
            'header'  => $header,
            'details' => $details,
        ]);
    }

    public function storeReturPenjualan(Request $request)
    {
        $request->validate([
            'tgl_penjualan'        => 'required|date',
            'kode_customer'        => 'required|string',
            'alamat'               => 'required|string|max:250',
            'kode_pesanan'         => 'required|string|exists:transaksi_penjualan,kode_pesanan',
            'keterangan'           => 'nullable|string|max:250',
            'items'                => 'required|array|min:1',
            'items.*.kode_barang'  => 'required|string',
            'items.*.nama_barang'  => 'required|string',
            'items.*.qty_retur'    => 'required|numeric|min:1',
        ]);

        $user = Auth::user();

        try {
            DB::transaction(function () use ($request, $user, &$kodeRetur) {
                $kodeRetur = $this->generateKodeReturPenjualanLocked();

                $penjualan = DB::table('transaksi_penjualan')
                    ->where('kode_pesanan', $request->kode_pesanan)
                    ->lockForUpdate()
                    ->first();

                if ($penjualan->status_pesanan !== 'Selesai') {
                    throw new \Exception('Retur penjualan hanya dapat dilakukan untuk pesanan dengan status Selesai.');
                }

                if ($this->isCustomerRole($user->kode_role)) {
                    $customerAktif = $this->getCustomerAktifByUser();

                    if (!$customerAktif || empty($customerAktif->kode_customer)) {
                        throw new \Exception('Customer untuk user login tidak ditemukan.');
                    }

                    if ($penjualan->kode_customer !== $customerAktif->kode_customer) {
                        throw new \Exception('Anda tidak memiliki akses untuk retur penjualan ini.');
                    }

                    $kodeCustomer = $customerAktif->kode_customer;
                } else {
                    if ($penjualan->kode_customer !== $request->kode_customer) {
                        throw new \Exception('Customer tidak sesuai dengan data penjualan.');
                    }

                    $kodeCustomer = $request->kode_customer;
                }

                DB::table('retur_penjualan')->insert([
                    'kode_rpenjualan' => $kodeRetur,
                    'tgl_penjualan'   => $request->tgl_penjualan,
                    'kode_customer'   => $kodeCustomer,
                    'alamat'          => $request->alamat,
                    'kode_pesanan'    => $request->kode_pesanan,
                    'keterangan'      => $request->keterangan,
                ]);

                $detailRows = [];

                foreach ($request->items as $item) {
                    $detailPenjualan = DB::table('transaksi_penjualan_detail')
                        ->where('kode_pesanan', $request->kode_pesanan)
                        ->where('kode_barang', $item['kode_barang'])
                        ->lockForUpdate()
                        ->first();

                    if (!$detailPenjualan) {
                        throw new \Exception("Barang {$item['kode_barang']} tidak ditemukan pada transaksi penjualan {$request->kode_pesanan}.");
                    }

                    $qtyRetur = (float) $item['qty_retur'];
                    $qtyJual = (float) $detailPenjualan->qty;

                    $qtySudahDiretur = DB::table('retur_penjualan_detail')
                        ->where('kode_pesanan', $request->kode_pesanan)
                        ->where('kode_barang', $item['kode_barang'])
                        ->sum('qty_retur');

                    $sisaBisaDiretur = $qtyJual - (float) $qtySudahDiretur;

                    if ($qtyRetur > $sisaBisaDiretur) {
                        throw new \Exception("Qty retur barang {$item['kode_barang']} melebihi sisa qty yang dapat diretur. Sisa retur: {$sisaBisaDiretur}.");
                    }

                    $hargaSatuan = (float) $detailPenjualan->harga_satuan;
                    $subtotalRetur = $qtyRetur * $hargaSatuan;

                    $detailRows[] = [
                        'kode_rpenjualan' => $kodeRetur,
                        'kode_pesanan'    => $request->kode_pesanan,
                        'kode_barang'     => $detailPenjualan->kode_barang,
                        'nama_barang'     => $detailPenjualan->nama_barang,
                        'qty_jual'        => $qtyJual,
                        'qty_retur'       => $qtyRetur,
                        'harga_satuan'    => $hargaSatuan,
                        'subtotal_retur'  => $subtotalRetur,
                        'date_entry'      => now(),
                    ];

                    /*
                        Jangan update master_barang di sini jika Anda memakai trigger.

                        Trigger retur_penjualan_detail AFTER INSERT akan menambah stok:
                        UPDATE master_barang
                        SET kapasitas = kapasitas + NEW.qty_retur
                        WHERE kode_barang = NEW.kode_barang;
                    */
                }

                DB::table('retur_penjualan_detail')->insert($detailRows);
            });

            return response()->json([
                'success'         => true,
                'message'         => 'Retur penjualan berhasil disimpan.',
                'kode_rpenjualan' => $kodeRetur,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan retur penjualan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function listReturPenjualan()
    {
        $user = Auth::user();

        $query = DB::table('retur_penjualan as rp')
            ->leftJoin('master_customer as mc', 'rp.kode_customer', '=', 'mc.kode_customer')
            ->select(
                'rp.kode_rpenjualan',
                'rp.tgl_penjualan',
                'rp.kode_pesanan',
                'rp.kode_customer',
                'mc.nama_customer',
                'rp.alamat',
                'rp.keterangan'
            )
            ->orderByDesc('rp.tgl_penjualan')
            ->orderByDesc('rp.kode_rpenjualan');

        if ($this->isCustomerRole($user->kode_role)) {
            $customerAktif = $this->getCustomerAktifByUser();

            if (!$customerAktif || empty($customerAktif->kode_customer)) {
                return response()->json(['data' => []]);
            }

            $query->where('rp.kode_customer', $customerAktif->kode_customer);
        }

        $data = $query->get();

        return response()->json(['data' => $data]);
    }

    public function searchRajaOngkirDestination(Request $request)
    {
        $keyword = $request->query('search');

        if (!$keyword || strlen($keyword) < 3) {
            return response()->json([
                'success' => true,
                'data' => [],
            ]);
        }

        try {
            $response = Http::withHeaders([
                'key' => config('services.rajaongkir.key'),
            ])->get(config('services.rajaongkir.base_url') . '/destination/domestic-destination', [
                'search' => $keyword,
                'limit' => 10,
                'offset' => 0,
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil data tujuan RajaOngkir.',
                    'detail' => $response->json(),
                ], 500);
            }

            return response()->json([
                'success' => true,
                'data' => $response->json('data') ?? [],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal koneksi ke RajaOngkir: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function checkRajaOngkir(Request $request)
    {
        $request->validate([
            'destination' => 'required|string',
            'weight' => 'required|numeric|min:1',
            'courier' => 'required|string',
        ]);

        try {
            $response = Http::asForm()
                ->withHeaders([
                    'key' => config('services.rajaongkir.key'),
                ])
                ->post(config('services.rajaongkir.base_url') . '/calculate/domestic-cost', [
                    'origin' => config('services.rajaongkir.origin'),
                    'destination' => $request->destination,
                    'weight' => $request->weight,
                    'courier' => $request->courier,
                ]);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengecek ongkir RajaOngkir.',
                    'detail' => $response->json(),
                ], 500);
            }

            return response()->json([
                'success' => true,
                'data' => $response->json('data') ?? [],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal koneksi ke RajaOngkir: ' . $e->getMessage(),
            ], 500);
        }
    }


}