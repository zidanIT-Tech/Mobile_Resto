<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Orderan;
use App\Models\Detail_orderan;
use App\Models\Menu;
use App\Models\Meja;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // Fungsi 1: Menampilkan Halaman
    public function index()
    {
        // Ambil semua menu
        $menus = Menu::all();

        // Ambil semua meja (untuk pilihan di modal nanti)
        $mejas = Meja::all();

        // Kirim data ke view
        return view('order.index', compact('menus', 'mejas'));
    }

    // Fungsi 2: Simpan Pesanan (Kita kosongkan dulu, fokus tampil dulu)
    public function store(Request $request)
    {
        $request->validate([
            'nama_konsumen' => 'required|string',
            'id_meja' => 'nullable|exists:mejas,id',
            'items' => 'required|array',
            'jenis_pesanan' => 'required|in:dinein,takeaway',
            'metode_pembayaran' => 'required|string', // Pastikan ini divalidasi
        ]);

        try {
            DB::beginTransaction();

            $total_bayar = 0;

            // ... (Bagian Cek Stok & Hitung Total SAMA SEPERTI SEBELUMNYA) ...
            foreach ($request->items as $item) {
                $menu = Menu::where('id', $item['id_menu'])->lockForUpdate()->first();
                if (!$menu) throw new \Exception("Menu hilang.");
                if ($menu->stok_porsi < $item['jumlah']) throw new \Exception("Stok habis.");

                $total_bayar += $menu->harga * $item['jumlah'];
                $menu->decrement('stok_porsi', $item['jumlah']);
            }

            // SIMPAN HEADER ORDER
            $orderan = Orderan::create([
                'id_user' => auth()->id() ?? null,
                'id_meja' => $request->id_meja,
                'nama_konsumen' => $request->nama_konsumen,
                'total_bayar' => $total_bayar,
                'status' => 'pending',
                'tanggal_orderan' => now(),

                // --- PERBAIKAN DI SINI ---
                // Pastikan baris ini TIDAK dikomentari //
                'metode_pembayaran' => $request->metode_pembayaran,
            ]);

            // ... (Bagian Simpan Detail SAMA SEPERTI SEBELUMNYA) ...
            foreach ($request->items as $item) {
                $menu = Menu::find($item['id_menu']);
                Detail_orderan::create([
                    'id_orderan' => $orderan->id,
                    'id_menu' => $item['id_menu'],
                    'jumlah' => $item['jumlah'],
                    'subtotal' => $menu->harga * $item['jumlah'],
                    'status' => 'processing',
                    'catatan' => $request->catatan,
                    'metode_pesanan' => $request->jenis_pesanan
                ]);
            }

            if ($request->jenis_pesanan == 'dinein' && $request->id_meja) {
                Meja::where('id', $request->id_meja)->update(['status' => 'booking']);
            }

            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'Pesanan sukses']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
}
