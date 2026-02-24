<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;


class Menu extends Model
{
    protected $fillable = [
        'nama_menu',
        'harga',
        'id_kategori',
        'stok_porsi',
        'foto',
        'deskripsi',
        'status'
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    public function detailOrderans()
    {
        return $this->hasMany(Detail_orderan::class, 'id_menu');
    }

    public function stokHarians()
    {
        return $this->hasMany(Update_stokharian::class, 'id_menu');
    }
}

