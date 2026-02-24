<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpdateStokHarian extends Model
{
    use HasFactory;
    protected $table = 'update_stokharians';

    protected $fillable = [
        'id_menu',
        'jumlah_porsi',
        'tanggal_update'
    ];

    // Relasi ke Menu (opsional, tapi bagus untuk masa depan)
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'id_menu');
    }
}