<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'master_customer';
    protected $primaryKey = 'kode_customer';

    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kode_customer',
        'kode_user',
        'nama_customer',
        'nohp_customer',
        'alamat_customer',
        'email_customer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'kode_user', 'kode_user');
    }
}