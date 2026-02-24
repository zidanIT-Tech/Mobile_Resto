<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meja extends Model
{
    protected $fillable = [
        'nomor_meja',
        'status',
        'kapasitas'
    ];

    public function orderans()
    {
        return $this->hasMany(Orderan::class, 'id_meja');
    }
}

