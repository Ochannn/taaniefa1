<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanStokBarang extends Model
{
    protected $table = 'laporan_stok_barang';
    protected $primaryKey = 'kode_barang';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
}