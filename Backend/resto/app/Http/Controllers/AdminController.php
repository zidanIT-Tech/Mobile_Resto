<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Orderan;
use App\Models\Menu;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    // Menampilkan dashboard admin
    public function index()
    {
        // Statistik dasar untuk dashboard
        $total_pendapatan = Orderan::where('status', 'dibayar')->sum('total_bayar');
        $total_order = Orderan::count();
        $total_menu = Menu::count();
        $total_pegawai = User::whereIn('role', ['kasir', 'chef'])->count();

        return view('admin.index', compact('total_pendapatan', 'total_order', 'total_menu', 'total_pegawai'));
    }

    // Menampilkan halaman kelola pegawai
    public function users()
    {
        // Ambil semua user kecuali Admin sendiri
        $users = User::orderBy('role', 'asc')->get();
        return view('admin.users', compact('users'));
    }

    // Logika tambah pegawai baru
    public function userStore(Request $request)
    {
        // Validasi input
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,kasir,chef',
        ]);

        // Buat user baru
        User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => 'aktif'
        ]);

        return redirect()->back()->with('success', 'Pegawai berhasil ditambahkan!');
    }

    // Logika hapus pegawai
    public function userDestroy($id)
    {
        $user = User::findOrFail($id);

        // Cegah admin menghapus dirinya sendiri
        if (auth()->user()->id == $user->id) {
            return redirect()->back()->with('error', 'Anda tidak bisa menghapus akun sendiri!');
        }

        // Hapus user
        $user->delete();
        return redirect()->back()->with('success', 'Akun pegawai dihapus.');
    }

    // --- TAMBAHAN: UPDATE PEGAWAI ---
    public function userUpdate(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id, // Ignore email milik sendiri saat cek unik
            'role' => 'required|in:admin,kasir,chef',
            'password' => 'nullable|min:6' // Password opsional saat edit
        ]);

        $data = [
            'username' => $request->username,
            'email' => $request->email,
            'role' => $request->role,
        ];

        // Hanya update password jika input tidak kosong
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->back()->with('success', 'Data pegawai berhasil diperbarui!');
    }

    // --- 1. TAMPILKAN HALAMAN MENU ---
    public function menu()
    {
        $menus = Menu::with('kategori')->orderBy('created_at', 'desc')->get();
        $kategoris = \App\Models\Kategori::all(); // Untuk dropdown kategori
        return view('admin.menu', compact('menus', 'kategoris'));
    }

    // --- 2. SIMPAN MENU BARU ---
    public function menuStore(Request $request)
    {
        $request->validate([
            'nama_menu' => 'required|string|max:255',
            'harga' => 'required|integer',
            'stok_porsi' => 'required|integer',
            'id_kategori' => 'required|exists:kategoris,id',
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:20480', // Wajib ada foto
            'deskripsi' => 'nullable|string'
        ]);

        // Upload Foto
        $pathFoto = null;
        if ($request->hasFile('foto')) {
            $pathFoto = $request->file('foto')->store('menu_photos', 'public');
        }

        Menu::create([
            'nama_menu' => $request->nama_menu,
            'harga' => $request->harga,
            'stok_porsi' => $request->stok_porsi,
            'id_kategori' => $request->id_kategori,
            'deskripsi' => $request->deskripsi,
            'foto' => $pathFoto
        ]);

        return redirect()->back()->with('success', 'Menu berhasil ditambahkan!');
    }

    // --- 3. UPDATE MENU ---
    public function menuUpdate(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $request->validate([
            'nama_menu' => 'required|string|max:255',
            'harga' => 'required|integer',
            'id_kategori' => 'required|exists:kategoris,id',
            'stok_porsi' => 'required|integer',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:20480',
        ]);

        $data = $request->except(['foto', '_token', '_method']);

        // Cek jika user upload foto baru
        if ($request->hasFile('foto')) {
            // Hapus foto lama biar server gak penuh
            if ($menu->foto && Storage::exists('public/' . $menu->foto)) {
                Storage::delete('public/' . $menu->foto);
            }
            // Simpan foto baru
            $data['foto'] = $request->file('foto')->store('menu_photos', 'public');
        }

        $menu->update($data);

        return redirect()->back()->with('success', 'Menu berhasil diperbarui!');
    }

    // --- 4. HAPUS MENU ---
    public function menuDestroy($id)
    {
        $menu = Menu::findOrFail($id);

        // Hapus file foto dari penyimpanan
        if ($menu->foto && Storage::exists('public/' . $menu->foto)) {
            Storage::delete('public/' . $menu->foto);
        }

        $menu->delete();
        return redirect()->back()->with('success', 'Menu berhasil dihapus.');
    }

    // --- 1. HALAMAN KELOLA KATEGORI ---
    public function kategori()
    {
        // Ambil kategori beserta jumlah menu yang ada di dalamnya (optional, biar keren)
        $kategoris = \App\Models\Kategori::withCount('menus')->get();
        return view('admin.kategori', compact('kategoris'));
    }

    // --- 2. SIMPAN KATEGORI BARU ---
    public function kategoriStore(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategoris,nama_kategori'
        ]);

        \App\Models\Kategori::create([
            'nama_kategori' => $request->nama_kategori
        ]);

        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan!');
    }

    // --- 3. UPDATE KATEGORI ---
    public function kategoriUpdate(Request $request, $id)
    {
        $kategori = \App\Models\Kategori::findOrFail($id);

        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategoris,nama_kategori,' . $id
        ]);

        $kategori->update([
            'nama_kategori' => $request->nama_kategori
        ]);

        return redirect()->back()->with('success', 'Kategori berhasil diperbarui!');
    }

    // --- 4. HAPUS KATEGORI ---
    public function kategoriDestroy($id)
    {
        $kategori = \App\Models\Kategori::findOrFail($id);

        // Cek apakah ada menu yang menggunakan kategori ini?
        if ($kategori->menus()->count() > 0) {
            return redirect()->back()->with('error', 'Gagal hapus! Masih ada menu yang menggunakan kategori ini.');
        }

        $kategori->delete();
        return redirect()->back()->with('success', 'Kategori berhasil dihapus.');
    }

    // --- 1. HALAMAN KELOLA MEJA ---
    public function meja()
    {
        // Urutkan berdasarkan nomor meja
        $mejas = \App\Models\Meja::orderBy('nomor_meja', 'asc')->get();
        return view('admin.meja', compact('mejas'));
    }

    // --- 2. SIMPAN MEJA BARU ---
    public function mejaStore(Request $request)
    {
        $request->validate([
            'nomor_meja' => 'required|string|unique:mejas,nomor_meja',
            'kapasitas' => 'required|integer|min:1'
        ]);

        \App\Models\Meja::create([
            'nomor_meja' => $request->nomor_meja,
            'kapasitas' => $request->kapasitas,
            'status' => 'available' // Default kosong
        ]);

        return redirect()->back()->with('success', 'Meja berhasil ditambahkan!');
    }

    // --- 3. UPDATE MEJA ---
    public function mejaUpdate(Request $request, $id)
    {
        $meja = \App\Models\Meja::findOrFail($id);

        $request->validate([
            'nomor_meja' => 'required|string|unique:mejas,nomor_meja,' . $id,
            'kapasitas' => 'required|integer|min:1',
            'status' => 'required|in:available,booking' // Admin bisa reset status jika meja error
        ]);

        $meja->update([
            'nomor_meja' => $request->nomor_meja,
            'kapasitas' => $request->kapasitas,
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Data meja berhasil diperbarui!');
    }

    // --- 4. HAPUS MEJA ---
    public function mejaDestroy($id)
    {
        $meja = \App\Models\Meja::findOrFail($id);

        // Cek apakah meja sedang dipakai (status booking)?
        if ($meja->status == 'booking') {
            return redirect()->back()->with('error', 'Gagal hapus! Meja sedang digunakan oleh pelanggan.');
        }

        $meja->delete();
        return redirect()->back()->with('success', 'Meja berhasil dihapus.');
    }
}
