<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{

    public function riwayatPenjualan()
    {
        $user = Auth::user();

        return view('transaksi.riwayatpenjualan', [
            'user' => $user,
        ]);
    }
    
    public function pembelian()
    {
        $suppliers = DB::table('master_supplier')
            ->select('kode_supplier', 'nama_supplier')
            ->orderBy('nama_supplier')
            ->get();

        $barangs = DB::table('master_barang')
            ->select('kode_barang', 'nama_barang', 'kapasitas')
            ->orderBy('nama_barang')
            ->get();

        return view('transaksi.transaksi_pembelian', [
            'kodePreview' => $this->previewKodePembelian(),
            'suppliers'   => $suppliers,
            'barangs'     => $barangs,
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
                'tp.alamat_kirim_pesanan',
                'tp.ongkir_pesanan',
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

        $barangs = DB::table('master_barang')
            ->select('kode_barang', 'nama_barang', 'harga_jual', 'kapasitas')
            ->orderBy('nama_barang')
            ->get();

        $customers = collect();

        if ($this->isAdminRole($user->kode_role)) {
            $customers = DB::table('master_customer')
                ->select('kode_customer', 'nama_customer')
                ->orderBy('nama_customer')
                ->get();
        }

        return view('transaksi.transaksi_penjualan', [
            'kodePreview'   => $this->previewKodePesanan(),
            'customerAktif' => $customerAktif,
            'customers'     => $customers,
            'barangs'       => $barangs,
            'user'          => $user,
        ]);
    }

    public function storePenjualan(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'tgl_pesanan'          => 'required|date',
            'jenis_pengiriman'     => 'required|string|in:Reguler,Express,Preorder',
            'jenis_pemesanan'      => 'required|string|in:Standart,Custom',
            'alamat_kirim_pesanan' => 'nullable|string',
            'ongkir_pesanan'       => 'required|numeric|min:0',
            'catatan_pesanan'      => 'nullable|string',
            'spesifikasi_tambahan' => 'nullable|string',
            'harga_estimasi'       => 'nullable|numeric|min:0',
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

        if ($request->jenis_pemesanan === 'Custom' && empty($request->spesifikasi_tambahan)) {
            return response()->json([
                'success' => false,
                'message' => 'Spesifikasi tambahan wajib diisi untuk pesanan custom.'
            ], 422);
        }

        if (!$this->validateOngkirByJenis($request->jenis_pengiriman, $request->ongkir_pesanan)) {
            return response()->json([
                'success' => false,
                'message' => 'Nilai ongkir tidak sesuai dengan jenis pengiriman.'
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
            DB::transaction(function () use ($request, $user, $kodeCustomer, $statusPesanan, &$kodePesanan, &$totalDetail, &$grandTotal) {
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

                $statusCustom = null;
                $spesifikasiTambahan = null;
                $hargaEstimasi = null;

                if ($request->jenis_pemesanan === 'Custom') {
                    $spesifikasiTambahan = $request->spesifikasi_tambahan;
                    $hargaEstimasi = $this->isAdminRole($user->kode_role) && $request->filled('harga_estimasi')
                        ? (float) $request->harga_estimasi
                        : null;

                    $statusCustom = $this->isCustomerRole($user->kode_role) ? null : 'lanjutkan';
                }

                DB::table('transaksi_penjualan')->insert([
                    'kode_pesanan'         => $kodePesanan,
                    'tgl_pesanan'          => $request->tgl_pesanan,
                    'kode_customer'        => $kodeCustomer,
                    'jenis_pengiriman'     => $request->jenis_pengiriman,
                    'jenis_pemesanan'      => $request->jenis_pemesanan,
                    'status_pesanan'       => $statusPesanan,
                    'alamat_kirim_pesanan' => $request->alamat_kirim_pesanan,
                    'ongkir_pesanan'       => (float) $request->ongkir_pesanan,
                    'catatan_pesanan'      => $request->catatan_pesanan,
                    'spesifikasi_tambahan' => $request->jenis_pemesanan === 'Custom' ? $spesifikasiTambahan : null,
                    'harga_estimasi'       => $request->jenis_pemesanan === 'Custom' ? $hargaEstimasi : null,
                    'status_custom'        => $request->jenis_pemesanan === 'Custom' ? $statusCustom : null,
                ]);

                DB::table('transaksi_penjualan_detail')->insert($detailRows);

                $grandTotal = $totalDetail + (float) $request->ongkir_pesanan;
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

    public function listPenjualan()
    {
        $user = Auth::user();

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
                'tp.harga_estimasi',
                'tp.status_custom'
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
            'custom'   => [
                'spesifikasi_tambahan' => $header->spesifikasi_tambahan,
                'harga_estimasi'       => $header->harga_estimasi,
                'status_custom'        => $header->status_custom,
            ],
            'can_edit' => $this->isAdminRole($user->kode_role) || $header->status_pesanan === 'Pending',
        ]);
    }

    public function updatePenjualan(Request $request, $kode_pesanan)
    {
        $user = Auth::user();

        $rules = [
            'tgl_pesanan'          => 'required|date',
            'jenis_pengiriman'     => 'required|string|in:Reguler,Express,Preorder',
            'jenis_pemesanan'      => 'required|string|in:Standart,Custom',
            'alamat_kirim_pesanan' => 'nullable|string',
            'ongkir_pesanan'       => 'required|numeric|min:0',
            'catatan_pesanan'      => 'nullable|string',
            'spesifikasi_tambahan' => 'nullable|string',
            'harga_estimasi'       => 'nullable|numeric|min:0',
            'status_custom'        => 'nullable|string|in:lanjutkan,batal',
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

        if ($request->jenis_pemesanan === 'Custom' && empty($request->spesifikasi_tambahan)) {
            return response()->json([
                'success' => false,
                'message' => 'Spesifikasi tambahan wajib diisi untuk pesanan custom.'
            ], 422);
        }

        if (!$this->validateOngkirByJenis($request->jenis_pengiriman, $request->ongkir_pesanan)) {
            return response()->json([
                'success' => false,
                'message' => 'Nilai ongkir tidak sesuai dengan jenis pengiriman.'
            ], 422);
        }

        $headerAkses = $this->getPenjualanHeaderForUser($kode_pesanan, $user);

        if (!$headerAkses) {
            return response()->json([
                'success' => false,
                'message' => 'Data penjualan tidak ditemukan atau Anda tidak memiliki akses.'
            ], 404);
        }

        if ($headerAkses->status_pesanan !== 'Pending') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan sudah diproses, data tidak dapat diedit lagi.'
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
                $user,
                $kode_pesanan,
                $kodeCustomer,
                $statusPesanan,
                $headerAkses,
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

                $spesifikasiTambahan = null;
                $hargaEstimasi = null;
                $statusCustom = null;

                if ($request->jenis_pemesanan === 'Custom') {
                    $spesifikasiTambahan = $request->spesifikasi_tambahan;

                    if ($this->isAdminRole($user->kode_role)) {
                        $hargaEstimasi = $request->filled('harga_estimasi')
                            ? (float) $request->harga_estimasi
                            : $header->harga_estimasi;
                    } else {
                        $hargaEstimasi = $header->harga_estimasi;
                    }

                    if ($this->isAdminRole($user->kode_role)) {
                        $statusCustom = $request->filled('status_custom')
                            ? $request->status_custom
                            : $header->status_custom;
                    } else {
                        $statusCustom = $header->status_custom;
                    }
                }

                DB::table('transaksi_penjualan')
                    ->where('kode_pesanan', $kode_pesanan)
                    ->update([
                        'tgl_pesanan'          => $request->tgl_pesanan,
                        'kode_customer'        => $kodeCustomer,
                        'jenis_pengiriman'     => $request->jenis_pengiriman,
                        'jenis_pemesanan'      => $request->jenis_pemesanan,
                        'status_pesanan'       => $statusPesanan,
                        'alamat_kirim_pesanan' => $request->alamat_kirim_pesanan,
                        'ongkir_pesanan'       => (float) $request->ongkir_pesanan,
                        'catatan_pesanan'      => $request->catatan_pesanan,
                        'spesifikasi_tambahan' => $request->jenis_pemesanan === 'Custom' ? $spesifikasiTambahan : null,
                        'harga_estimasi'       => $request->jenis_pemesanan === 'Custom' ? $hargaEstimasi : null,
                        'status_custom'        => $request->jenis_pemesanan === 'Custom' ? $statusCustom : null,
                    ]);

                DB::table('transaksi_penjualan_detail')->insert($detailRows);

                $grandTotal = $totalDetail + (float) $request->ongkir_pesanan;
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

        if ($headerAkses->status_pesanan !== 'Pending') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan yang sudah diproses tidak dapat dihapus.'
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
}