<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'master_user';
    protected $primaryKey = 'kode_user';

    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kode_user',
        'kode_role',
        'nama_user',
        'email_user',
        'pw_user',
    ];

    protected $hidden = [
        'pw_user',
    ];

    public function getAuthPassword()
    {
        return $this->pw_user;
    }

    public function getAuthIdentifierName()
    {
        return 'kode_user';
    }

    public function isAdmin()
    {
        return $this->kode_role === 'KRL001';
    }

    public function isKaryawan()
    {
        return $this->kode_role === 'KRL002';
    }

    public function isCustomer()
    {
        return $this->kode_role === 'KRL003';
    }
}