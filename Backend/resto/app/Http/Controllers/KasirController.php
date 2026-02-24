<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Orderan;
use App\Models\Meja;

class KasirController extends Controller
{
    public function index()
    {
        $orderans = Orderan::where('status', 'pending')
            ->with(['meja', 'detailOrderans']) // Eager load biar cepat
            ->orderBy('created_at', 'desc') // Pesanan terbaru di atas
            ->get();

        $mejas = Meja::orderBy('nomor_meja', 'asc')->get();

        return view('kasir.index', compact('orderans', 'mejas'));
    }

    public function detail($id)
    {
        // Untuk melihat detail pesanan sebelum bayar
        $order = Orderan::with(['detailOrderans.menu', 'meja'])->findOrFail($id);
        return view('kasir.detail', compact('order'));
    }

    public function bayar(Request $request, $id)
    {
        $order = Orderan::findOrFail($id);

        // 1. Update Status Order jadi 'paid' (atau 'dibayar' sesuai enum DB kamu)
        $order->update(['status' => 'dibayar']);

        // 2. Cek: Kalau ada Meja (Dine In), baru kosongkan mejanya.
        // Kalau Take Away (null), lewati langkah ini agar tidak error 404.
        if ($order->id_meja != null) {
            $meja = Meja::find($order->id_meja);
            // Pastikan data meja-nya ada, baru update
            if ($meja) {
                $meja->update(['status' => 'available']);
            }
        }

        // 3. Redirect kembali ke halaman daftar kasir
        return redirect()->route('kasir.index')->with('success', 'Pembayaran Berhasil!');
    }
}
