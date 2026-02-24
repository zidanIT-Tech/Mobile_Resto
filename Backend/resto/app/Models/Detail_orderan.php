<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detail_orderan extends Model
{
    protected $fillable = [
        'id_orderan',
        'id_menu',
        'jumlah',
        'metode_pesanan',
        'catatan',
        'status',
        'subtotal'
    ];

    public function orderan()
    {
        return $this->belongsTo(Orderan::class, 'id_orderan');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'id_menu');
    }
}

