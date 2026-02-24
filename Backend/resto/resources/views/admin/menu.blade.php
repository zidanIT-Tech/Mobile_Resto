@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">🍔 Kelola Menu Makanan</h1>
        
        <button onclick="document.getElementById('modal-add').classList.remove('hidden')" class="bg-orange-600 text-white px-4 py-2 rounded-xl font-bold shadow hover:bg-orange-700 transition flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" /></svg>
            Tambah Menu
        </button>
    </div>

    @if(session('success'))
    <div class="bg-green-100 text-green-700 p-3 rounded-lg mb-4 border border-green-200">{{ session('success') }}</div>
    @endif
    
    @if ($errors->any())
    <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-4 border border-red-200">
        <ul class="list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="p-4 text-sm text-gray-500 w-20">Foto</th>
                    <th class="p-4 text-sm text-gray-500">Nama Menu</th>
                    <th class="p-4 text-sm text-gray-500">Kategori</th>
                    <th class="p-4 text-sm text-gray-500">Harga</th>
                    <th class="p-4 text-sm text-gray-500">Stok</th>
                    <th class="p-4 text-sm text-gray-500 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($menus as $menu)
                <tr class="hover:bg-gray-50">
                    <td class="p-4">
                        <div class="h-12 w-12 rounded-lg bg-gray-100 overflow-hidden border">
                            @if($menu->foto)
                                <img src="{{ asset('storage/' . $menu->foto) }}" class="h-full w-full object-cover">
                            @else
                                <span class="text-[10px] text-gray-400 flex items-center justify-center h-full">No IMG</span>
                            @endif
                        </div>
                    </td>
                    <td class="p-4 font-bold text-gray-700">{{ $menu->nama_menu }}</td>
                    <td class="p-4 text-sm text-gray-600">
                        <span class="px-2 py-1 rounded bg-gray-100 text-xs font-bold">{{ $menu->kategori->nama_kategori ?? '-' }}</span>
                    </td>
                    <td class="p-4 text-sm text-orange-600 font-bold">Rp {{ number_format($menu->harga) }}</td>
                    <td class="p-4 text-sm {{ $menu->stok_porsi < 5 ? 'text-red-500 font-bold' : 'text-gray-600' }}">{{ $menu->stok_porsi }}</td>
                    
                    <td class="p-4 text-right flex justify-end gap-2">
                        <button onclick="document.getElementById('modal-edit-{{ $menu->id }}').classList.remove('hidden')" class="text-blue-500 hover:text-blue-700 font-bold text-sm bg-blue-50 px-3 py-1 rounded-lg">Edit</button>
                        
                        <form action="{{ route('admin.menu.destroy', $menu->id) }}" method="POST" onsubmit="return confirm('Yakin hapus menu ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-sm bg-red-50 px-3 py-1 rounded-lg">Hapus</button>
                        </form>
                    </td>
                </tr>

                <div id="modal-edit-{{ $menu->id }}" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4 backdrop-blur-sm">
                    <div class="bg-white w-full max-w-lg rounded-2xl p-6 animate-fade-in-up max-h-[90vh] overflow-y-auto shadow-2xl">
                        <h2 class="text-xl font-bold mb-4 text-gray-800">✏️ Edit Menu</h2>
                        <form action="{{ route('admin.menu.update', $menu->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama Menu</label>
                                    <input type="text" name="nama_menu" value="{{ $menu->nama_menu }}" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-orange-500 outline-none" required>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Harga (Rp)</label>
                                        <input type="number" name="harga" value="{{ $menu->harga }}" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-orange-500 outline-none" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Stok Awal</label>
                                        <input type="number" name="stok_porsi" value="{{ $menu->stok_porsi }}" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-orange-500 outline-none" required>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Kategori</label>
                                    <select name="id_kategori" class="w-full border rounded-lg p-2 bg-white focus:ring-2 focus:ring-orange-500 outline-none">
                                        @foreach($kategoris as $kat)
                                            <option value="{{ $kat->id }}" {{ $menu->id_kategori == $kat->id ? 'selected' : '' }}>{{ $kat->nama_kategori }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Ganti Foto (Opsional)</label>
                                    <input type="file" name="foto" class="w-full border rounded-lg p-2 text-sm">
                                    <p class="text-xs text-gray-400 mt-1">Biarkan kosong jika tidak ingin mengganti foto.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Deskripsi</label>
                                    <textarea name="deskripsi" rows="3" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-orange-500 outline-none">{{ $menu->deskripsi }}</textarea>
                                </div>
                            </div>
                            <div class="flex gap-3 mt-6">
                                <button type="button" onclick="document.getElementById('modal-edit-{{ $menu->id }}').classList.add('hidden')" class="flex-1 py-2 border rounded-xl font-bold text-gray-600 hover:bg-gray-50 transition">Batal</button>
                                <button type="submit" class="flex-1 py-2 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-200">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
                @endforeach
            </tbody>
        </table>
    </div>

    <div id="modal-add" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4 backdrop-blur-sm">
        <div class="bg-white w-full max-w-lg rounded-2xl p-6 animate-fade-in-up max-h-[90vh] overflow-y-auto shadow-2xl">
            <h2 class="text-xl font-bold mb-4 text-gray-800">➕ Tambah Menu Baru</h2>
            <form action="{{ route('admin.menu.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nama Menu</label>
                        <input type="text" name="nama_menu" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-orange-500 outline-none" required placeholder="Contoh: Nasi Goreng Spesial">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Harga (Rp)</label>
                            <input type="number" name="harga" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-orange-500 outline-none" required placeholder="15000">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Stok Awal</label>
                            <input type="number" name="stok_porsi" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-orange-500 outline-none" required value="10">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Kategori</label>
                        <select name="id_kategori" class="w-full border rounded-lg p-2 bg-white focus:ring-2 focus:ring-orange-500 outline-none">
                            @foreach($kategoris as $kat)
                                <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Foto Menu</label>
                        <input type="file" name="foto" class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-orange-500 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="deskripsi" rows="3" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-orange-500 outline-none" placeholder="Jelaskan menu ini..."></textarea>
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('modal-add').classList.add('hidden')" class="flex-1 py-2 border rounded-xl font-bold text-gray-600 hover:bg-gray-50 transition">Batal</button>
                    <button type="submit" class="flex-1 py-2 bg-orange-600 text-white rounded-xl font-bold hover:bg-orange-700 transition shadow-lg shadow-orange-200">Simpan Menu</button>
                </div>
            </form>
        </div>
    </div>
@endsection