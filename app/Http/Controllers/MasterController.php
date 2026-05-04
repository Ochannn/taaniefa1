<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\LogAktivitasHelper;

class MasterController extends Controller
{
    private function getConfig($module)
    {
        $configs = [
            'kualitas' => [
                'table' => 'master_kualitas',
                'primaryKey' => 'kode_kualitas',
                'prefix' => 'KK',
                'title' => 'Kualitas',
                'useKodeUser' => true,
                'columns' => [
                    ['label' => 'Kode Kualitas', 'field' => 'kode_kualitas'],
                    ['label' => 'Kode User', 'field' => 'kode_user'],
                    ['label' => 'Nama Kualitas', 'field' => 'nama_kualitas'],
                ],
                'formFields' => [
                    ['name' => 'kode_kualitas', 'label' => 'Kode Kualitas', 'type' => 'text', 'readonly' => true],
                    ['name' => 'nama_kualitas', 'label' => 'Nama Kualitas', 'type' => 'text', 'readonly' => false],
                ],
                'validation' => [
                    'nama_kualitas' => 'required|string|max:100',
                ],
                'fillableFields' => ['nama_kualitas'],
            ],

            'jenis-palet' => [
                'table' => 'master_jenis_palet',
                'primaryKey' => 'kode_palet',
                'prefix' => 'KJP',
                'title' => 'Jenis Palet',
                'useKodeUser' => true,
                'columns' => [
                    ['label' => 'Kode Palet', 'field' => 'kode_palet'],
                    ['label' => 'Kode User', 'field' => 'kode_user'],
                    ['label' => 'Nama Palet', 'field' => 'nama_palet'],
                ],
                'formFields' => [
                    ['name' => 'kode_palet', 'label' => 'Kode Palet', 'type' => 'text', 'readonly' => true],
                    ['name' => 'nama_palet', 'label' => 'Nama Palet', 'type' => 'text', 'readonly' => false],
                ],
                'validation' => [
                    'nama_palet' => 'required|string|max:100',
                ],
                'fillableFields' => ['nama_palet'],
            ],
            'satuan' => [
                'table' => 'master_satuan',
                'primaryKey' => 'kode_satuan',
                'prefix' => 'KS',
                'title' => 'Master Satuan',
                'useKodeUser' => true,
                'columns' => [
                    ['label' => 'Kode Satuan', 'field' => 'kode_satuan'],
                    ['label' => 'Kode User', 'field' => 'kode_user'],
                    ['label' => 'Nama Satuan', 'field' => 'nama_satuan'],
                ],
                'formFields' => [
                    ['name' => 'kode_satuan', 'label' => 'Kode Satuan', 'type' => 'text', 'readonly' => true],
                    ['name' => 'nama_satuan', 'label' => 'Nama Satuan', 'type' => 'text', 'readonly' => false],
                ],
                'validation' => [
                    'nama_satuan' => 'required|string|max:100',
                ],
                'fillableFields' => ['nama_satuan'],
            ],
            'kategori' => [
                'table' => 'master_kategori_barang',
                'primaryKey' => 'kode_kategori',
                'prefix' => 'KKR',
                'title' => 'Master Kategori',
                'useKodeUser' => true,
                'columns' => [
                    ['label' => 'Kode Kategori', 'field' => 'kode_kategori'],
                    ['label' => 'Kode User', 'field' => 'kode_user'],
                    ['label' => 'Nama Kategori', 'field' => 'nama_kategori'],
                ],
                'formFields' => [
                    ['name' => 'kode_kategori', 'label' => 'Kode Kategori', 'type' => 'text', 'readonly' => true],
                    ['name' => 'nama_kategori', 'label' => 'Nama Kategori', 'type' => 'text', 'readonly' => false],
                ],
                'validation' => [
                    'nama_kategori' => 'required|string|max:100',
                ],
                'fillableFields' => ['nama_kategori'],
            ],
            'role' => [
                'table' => 'master_role',
                'primaryKey' => 'kode_role',
                'prefix' => 'KRL',
                'title' => 'Master Role',
                'useKodeUser' => false,
                'columns' => [
                    ['label' => 'Kode Role', 'field' => 'kode_role'],
                    ['label' => 'Nama Role', 'field' => 'nama_role'],
                ],
                'formFields' => [
                    ['name' => 'kode_role', 'label' => 'Kode Role', 'type' => 'text', 'readonly' => true],
                    ['name' => 'nama_role', 'label' => 'Nama Role', 'type' => 'text', 'readonly' => false],
                ],
                'validation' => [
                    'nama_role' => 'required|string|max:100',
                ],
                'fillableFields' => ['nama_role'],
            ],
            'user' => [
                'table' => 'master_user',
                'primaryKey' => 'kode_user',
                'prefix' => 'KUSR',
                'title' => 'Master User',
                'useKodeUser' => false,
                'columns' => [
                    ['label' => 'Kode User', 'field' => 'kode_user'],
                    ['label' => 'Kode Role', 'field' => 'kode_role'],
                    ['label' => 'Nama User', 'field' => 'nama_user'],
                    ['label' => 'Email User', 'field' => 'email_user'],
                    ['label' => 'Password User', 'field' => 'pw_user'],
                ],
                'formFields' => [
                    ['name' => 'kode_user', 'label' => 'Kode User', 'type' => 'text', 'readonly' => true],
                    ['name' => 'kode_role', 'label' => 'Kode Role', 'type' => 'select', 'options_key' => 'roles', 'readonly' => false],
                    ['name' => 'nama_user', 'label' => 'Nama User', 'type' => 'text', 'readonly' => false],
                    ['name' => 'email_user', 'label' => 'Email User', 'type' => 'email', 'readonly' => false],
                    ['name' => 'pw_user', 'label' => 'Password User', 'type' => 'text', 'readonly' => false],
                ],
                'validation' => [
                    'kode_role' => 'required|string|max:10',
                    'nama_user' => 'required|string|max:100',
                    'email_user' => 'required|email|max:100',
                    'pw_user' => 'required|string|max:255',
                ],
                'fillableFields' => ['kode_role', 'nama_user', 'email_user', 'pw_user'],
            ],
            'supplier' => [
                'table' => 'master_supplier',
                'primaryKey' => 'kode_supplier',
                'prefix' => 'KSP',
                'title' => 'Master Supplier',
                'useKodeUser' => true,
                'columns' => [
                    ['label' => 'Kode Supplier', 'field' => 'kode_supplier'],
                    ['label' => 'Kode User', 'field' => 'kode_user'],
                    ['label' => 'Nama Supplier', 'field' => 'nama_supplier'],
                    ['label' => 'No Hp Supplier', 'field' => 'nohp_supplier'],
                    ['label' => 'Alamat Supplier', 'field' => 'alamat_supplier'],
                    ['label' => 'Keterangan Supplier', 'field' => 'keterangan'],
                ],
                'formFields' => [
                    ['name' => 'kode_supplier', 'label' => 'Kode Supplier', 'type' => 'text', 'readonly' => true],
                    ['name' => 'nama_supplier', 'label' => 'Nama Supplier', 'type' => 'text', 'readonly' => false],
                    ['name' => 'nohp_supplier', 'label' => 'No Hp Supplier', 'type' => 'text', 'readonly' => false],
                    ['name' => 'alamat_supplier', 'label' => 'Alamat Supplier', 'type' => 'text', 'readonly' => false],
                    ['name' => 'keterangan', 'label' => 'Keterangan Supplier', 'type' => 'text', 'readonly' => false],
                ],
                'validation' => [
                    'nama_supplier' => 'required|string|max:100',
                    'nohp_supplier' => 'required|string|max:100',
                    'alamat_supplier' => 'required|string|max:255',
                    'keterangan' => 'required|string|max:255',
                ],
                'fillableFields' => ['nama_supplier', 'nohp_supplier', 'alamat_supplier', 'keterangan'],
            ],

            'karyawan' => [
                'table' => 'master_karyawan',
                'primaryKey' => 'kode_karyawan',
                'prefix' => 'KKY',
                'title' => 'Master Karyawan',
                'useKodeUser' => true,
                'columns' => [
                    ['label' => 'Kode Karyawan', 'field' => 'kode_karyawan'],
                    ['label' => 'Kode User', 'field' => 'kode_user'],
                    ['label' => 'Nama Karyawan', 'field' => 'nama_karyawan'],
                    ['label' => 'Jabatan Karyawan', 'field' => 'jabatan_karyawan'],
                    ['label' => 'Alamat Karyawan', 'field' => 'alamat_karyawan'],
                    ['label' => 'No Hp Karyawan', 'field' => 'nohp_karyawan'],
                ],
                'formFields' => [
                    ['name' => 'kode_karyawan', 'label' => 'Kode Karyawan', 'type' => 'text', 'readonly' => true],
                    ['name' => 'nama_karyawan', 'label' => 'Nama Karyawan', 'type' => 'text', 'readonly' => false],
                    ['name' => 'jabatan_karyawan', 'label' => 'Jabatan Karyawan', 'type' => 'text', 'readonly' => false],
                    ['name' => 'alamat_karyawan', 'label' => 'Alamat Karyawan', 'type' => 'text', 'readonly' => false],
                    ['name' => 'nohp_karyawan', 'label' => 'No Hp Karyawan', 'type' => 'text', 'readonly' => false],
                ],
                'validation' => [
                    'nama_karyawan' => 'required|string|max:100',
                    'jabatan_karyawan' => 'required|string|max:100',
                    'alamat_karyawan' => 'required|string|max:255',
                    'nohp_karyawan' => 'required|string|max:20',
                ],
                'fillableFields' => ['nama_karyawan', 'jabatan_karyawan', 'alamat_karyawan', 'nohp_karyawan'],
            ],

            'barang' => [
                'table' => 'master_barang',
                'primaryKey' => 'kode_barang',
                'prefix' => 'KBR',
                'title' => 'Master Barang',
                'useKodeUser' => true,
                'columns' => [
                    ['label' => 'Kode Barang', 'field' => 'kode_barang'],
                    ['label' => 'Nama Barang', 'field' => 'nama_barang'],
                    ['label' => 'Kode Kategori', 'field' => 'kode_kategori'],
                    ['label' => 'Kode Satuan', 'field' => 'kode_satuan'],
                    ['label' => 'Kode User', 'field' => 'kode_user'],
                    ['label' => 'Kapasitas', 'field' => 'kapasitas'],
                    ['label' => 'Harga Jual', 'field' => 'harga_jual'],
                    ['label' => 'Stok Minimum', 'field' => 'stok_minimum'],
                    ['label' => 'Deskripsi Barang', 'field' => 'deskripsi_barang'],
                ],
                'formFields' => [
                    ['name' => 'kode_barang', 'label' => 'Kode Barang', 'type' => 'text', 'readonly' => true],
                    ['name' => 'nama_barang', 'label' => 'Nama Barang', 'type' => 'text', 'readonly' => false],
                    ['name' => 'kode_kategori', 'label' => 'Kode Kategori', 'type' => 'select', 'options_key' => 'kategori', 'readonly' => false],
                    ['name' => 'kode_satuan', 'label' => 'Kode Satuan', 'type' => 'select', 'options_key' => 'satuan', 'readonly' => false],
                    ['name' => 'kapasitas', 'label' => 'Kapasitas', 'type' => 'text', 'readonly' => false],
                    ['name' => 'harga_jual', 'label' => 'Harga Jual', 'type' => 'number', 'readonly' => false],
                    ['name' => 'stok_minimum', 'label' => 'Stok Minimum', 'type' => 'number', 'readonly' => false],
                    ['name' => 'deskripsi_barang', 'label' => 'Deskripsi Barang', 'type' => 'text', 'readonly' => false],
                ],
                'validation' => [
                    'nama_barang' => 'required|string|max:100',
                    'kode_kategori' => 'required|string|max:10',
                    'kode_satuan' => 'required|string|max:10',
                    'kapasitas' => 'required|string|max:100',
                    'harga_jual' => 'required|numeric',
                    'stok_minimum' => 'required|numeric',
                    'deskripsi_barang' => 'required|string|max:255',
                ],
                'fillableFields' => [
                    'nama_barang',
                    'kode_kategori',
                    'kode_satuan',
                    'kapasitas',
                    'harga_jual',
                    'stok_minimum',
                    'deskripsi_barang',
                ],
            ],

            'rekening-pembayaran' => [
                'table' => 'master_rekening_pembayaran',
                'primaryKey' => 'kode_rekening',
                'prefix' => 'KRP',
                'title' => 'Master Rekening Pembayaran',
                'useKodeUser' => false,
                'columns' => [
                    ['label' => 'Kode Rekening', 'field' => 'kode_rekening'],
                    ['label' => 'Metode Pembayaran', 'field' => 'metode_pembayaran'],
                    ['label' => 'Nama Bank', 'field' => 'nama_bank'],
                    ['label' => 'Nomor Rekening', 'field' => 'nomor_rekening'],
                    ['label' => 'Atas Nama', 'field' => 'atas_nama'],
                    ['label' => 'Gambar QRIS', 'field' => 'gambar_qris'],
                    ['label' => 'Status Aktif', 'field' => 'status_aktif'],
                ],
                'formFields' => [
                    ['name' => 'kode_rekening', 'label' => 'Kode Rekening', 'type' => 'text', 'readonly' => true],
                    ['name' => 'metode_pembayaran', 'label' => 'Metode Pembayaran', 'type' => 'select', 'options_key' => 'metode_pembayaran', 'readonly' => false],
                    ['name' => 'nama_bank', 'label' => 'Nama Bank', 'type' => 'text', 'readonly' => false],
                    ['name' => 'nomor_rekening', 'label' => 'Nomor Rekening', 'type' => 'text', 'readonly' => false],
                    ['name' => 'atas_nama', 'label' => 'Atas Nama', 'type' => 'text', 'readonly' => false],
                    ['name' => 'gambar_qris', 'label' => 'Path Gambar QRIS', 'type' => 'text', 'readonly' => false],
                    ['name' => 'status_aktif', 'label' => 'Status Aktif', 'type' => 'select', 'options_key' => 'status_aktif', 'readonly' => false],
                ],
                'validation' => [
                    'metode_pembayaran' => 'required|string|max:50',
                    'nama_bank' => 'nullable|string|max:50',
                    'nomor_rekening' => 'nullable|string|max:100',
                    'atas_nama' => 'nullable|string|max:100',
                    'gambar_qris' => 'nullable|string|max:255',
                    'status_aktif' => 'required|in:0,1',
                ],
                'fillableFields' => [
                    'metode_pembayaran',
                    'nama_bank',
                    'nomor_rekening',
                    'atas_nama',
                    'gambar_qris',
                    'status_aktif',
                ],
            ],
        ];

        if (!isset($configs[$module])) {
            abort(404, 'Module master tidak ditemukan.');
        }

        return $configs[$module];
    }

    private function generateKode($table, $primaryKey, $prefix)
    {
        $lastKode = DB::table($table)
            ->orderBy($primaryKey, 'desc')
            ->value($primaryKey);

        if (!$lastKode) {
            return $prefix . '001';
        }

        $angka = (int) substr($lastKode, strlen($prefix));
        $angka++;

        return $prefix . str_pad($angka, 3, '0', STR_PAD_LEFT);
    }

    public function index($module)
    {
        $config = $this->getConfig($module);

        $rows = DB::table($config['table'])->get();
        $columns = $config['columns'];
        $formFields = $config['formFields'];
        $primaryKey = $config['primaryKey'];
        $editFields = array_column($formFields, 'name');
        $nextKode = $this->generateKode(
            $config['table'],
            $config['primaryKey'],
            $config['prefix']
        );

        $fieldOptions = [];

        if ($module === 'user') {
            $fieldOptions['roles'] = DB::table('master_role')
                ->select('kode_role', 'nama_role')
                ->orderBy('kode_role', 'asc')
                ->get()
                ->map(function ($item) {
                    return (object) [
                        'value' => $item->kode_role,
                        'text' => $item->kode_role . ' - ' . $item->nama_role,
                    ];
                });
        }

        if ($module === 'rekening-pembayaran') {
            $fieldOptions['metode_pembayaran'] = collect([
                (object) [
                    'value' => 'Transfer Bank',
                    'text' => 'Transfer Bank',
                ],
                (object) [
                    'value' => 'QRIS',
                    'text' => 'QRIS',
                ],
                (object) [
                    'value' => 'Cash',
                    'text' => 'Cash',
                ],
            ]);

            $fieldOptions['status_aktif'] = collect([
                (object) [
                    'value' => '1',
                    'text' => 'Aktif',
                ],
                (object) [
                    'value' => '0',
                    'text' => 'Tidak Aktif',
                ],
            ]);
        }

        if ($module === 'barang') {
            $fieldOptions['kategori'] = DB::table('master_kategori_barang')
                ->select('kode_kategori', 'nama_kategori')
                ->orderBy('kode_kategori', 'asc')
                ->get()
                ->map(function ($item) {
                    return (object) [
                        'value' => $item->kode_kategori,
                        'text' => $item->kode_kategori . ' - ' . $item->nama_kategori,
                    ];
                });

            $fieldOptions['satuan'] = DB::table('master_satuan')
                ->select('kode_satuan', 'nama_satuan')
                ->orderBy('kode_satuan', 'asc')
                ->get()
                ->map(function ($item) {
                    return (object) [
                        'value' => $item->kode_satuan,
                        'text' => $item->kode_satuan . ' - ' . $item->nama_satuan,
                    ];
                });
        }

        return view('master.master_template', compact(
            'module',
            'config',
            'rows',
            'columns',
            'formFields',
            'primaryKey',
            'editFields',
            'nextKode',
            'fieldOptions'
        ));
    }

    public function store(Request $request, $module)
    {
        $config = $this->getConfig($module);

        $request->validate($config['validation']);

        $newKode = $this->generateKode(
            $config['table'],
            $config['primaryKey'],
            $config['prefix']
        );

        $data = [
            $config['primaryKey'] => $newKode,
        ];

        if (!empty($config['useKodeUser'])) {
            $data['kode_user'] = Auth::user()->kode_user;
        }

        foreach ($config['fillableFields'] as $field) {
            $data[$field] = $request->$field;
        }

        DB::table($config['table'])->insert($data);

        LogAktivitasHelper::simpan(
            $newKode,
            $config['table'],
            'INSERT',
            null,
            null,
            $data
        );

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan.'
        ]);
    }

    public function update(Request $request, $module, $id)
    {
        $config = $this->getConfig($module);

        $request->validate($config['validation']);

        $data = [];

        if (!empty($config['useKodeUser'])) {
            $data['kode_user'] = Auth::user()->kode_user;
        }

        foreach ($config['fillableFields'] as $field) {
            $data[$field] = $request->$field;
        }

        $oldData = DB::table($config['table'])
            ->where($config['primaryKey'], $id)
            ->first();

        DB::table($config['table'])
            ->where($config['primaryKey'], $id)
            ->update($data);

        $newData = DB::table($config['table'])
            ->where($config['primaryKey'], $id)
            ->first();

        LogAktivitasHelper::simpan(
            $id,
            $config['table'],
            'UPDATE',
            $oldData,
            $data,
            $newData
        );

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diperbarui.'
        ]);
    }

    public function destroy($module, $id)
    {
        $config = $this->getConfig($module);

        $oldData = DB::table($config['table'])
            ->where($config['primaryKey'], $id)
            ->first();

        DB::table($config['table'])
            ->where($config['primaryKey'], $id)
            ->delete();

        LogAktivitasHelper::simpan(
            $id,
            $config['table'],
            'DELETE',
            $oldData,
            null,
            null
        );

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus.'
        ]);
    }
}