<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Orderan;
use App\Models\Detail_orderan;
use App\Models\Menu;
use App\Models\UpdateStokHarian;

class DapurController extends Controller
{
    public function index()
    {
        $orderans = Orderan::whereHas('detailOrderans', function ($q) {
            $q->where('status', 'processing');
        })
            ->with(['detailOrderans.menu', 'meja'])
            ->orderBy('created_at', 'asc') // Dahulukan pesanan lama
            ->get();

        return view('dapur.index', compact('orderans'));
    }

    public function selesai($id)
    {
        Detail_orderan::where('id_orderan', $id)
            ->update(['status' => 'done']);

        return redirect()->back()->with('success', 'Pesanan selesai dimasak!');
    }

    public function stok()
    {
        // Ambil semua menu
        $menus = Menu::orderBy('nama_menu', 'asc')->get();
        return view('dapur.stok', compact('menus'));
    }

    // --- FUNGSI BARU: UPDATE STOK ---
    public function updateStok(Request $request, $id)
    {
        // Validasi: Input adalah jumlah masakan baru (misal: 20 porsi), minimal 1
        $request->validate([
            'jumlah_porsi' => 'required|integer|min:1'
        ]);

        $menu = Menu::findOrFail($id);

        // 1. SIMPAN RIWAYAT KE TABEL update_stokharian (LOG HISTORY)
        UpdateStokHarian::create([
            'id_menu' => $id,
            'jumlah_porsi' => $request->jumlah_porsi, // Mencatat berapa yang dimasak
            'tanggal_update' => now()
        ]);

        // 2. UPDATE STOK REAL-TIME DI TABEL menus
        // Kita gunakan increment (tambah) bukan update replace, biar aman
        $menu->increment('stok_porsi', $request->jumlah_porsi);

        return redirect()->back()->with('success', 'Berhasil menambah ' . $request->jumlah_porsi . ' porsi untuk ' . $menu->nama_menu);
    }
}
