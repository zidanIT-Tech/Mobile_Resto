<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $fillable = [
        'nama_karyawan',
        'no_hp',
        'alamat',
        'jabatan'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id_karyawan');
    }
}

