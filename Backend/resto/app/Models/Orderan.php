<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orderan extends Model
{
    protected $fillable = [
        'nama_konsumen',
        'total_bayar',
        'tanggal_orderan',
        'status',
        'id_user',
        'id_meja',
        'metode_pembayaran'
    ];

    protected $casts = [
        'tanggal_orderan' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function meja()
    {
        return $this->belongsTo(Meja::class, 'id_meja');
    }

    public function detailOrderans()
    {
        return $this->hasMany(Detail_orderan::class, 'id_orderan');
    }
}

