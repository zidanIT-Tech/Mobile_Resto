@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">🪑 Kelola Meja Restoran</h1>
        
        <button onclick="document.getElementById('modal-add').classList.remove('hidden')" class="bg-orange-600 text-white px-4 py-2 rounded-xl font-bold shadow hover:bg-orange-700 transition flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" /></svg>
            Tambah Meja
        </button>
    </div>

    @if(session('success'))
    <div class="bg-green-100 text-green-700 p-3 rounded-lg mb-4 border border-green-200">{{ session('success') }}</div>
    @endif
    
    @if(session('error'))
    <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-4 border border-red-200">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden max-w-4xl">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="p-4 text-sm text-gray-500 w-20">Nomor</th>
                    <th class="p-4 text-sm text-gray-500">Kapasitas</th>
                    <th class="p-4 text-sm text-gray-500 text-center">Status Saat Ini</th>
                    <th class="p-4 text-sm text-gray-500 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($mejas as $meja)
                <tr class="hover:bg-gray-50">
                    <td class="p-4 font-bold text-gray-800 text-lg">{{ $meja->nomor_meja }}</td>
                    <td class="p-4 text-gray-600">{{ $meja->kapasitas }} Orang</td>
                    <td class="p-4 text-center">
                        @if($meja->status == 'available')
                            <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-xs font-bold uppercase">Kosong (Available)</span>
                        @else
                            <span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-xs font-bold uppercase animate-pulse">Sedang Dipakai</span>
                        @endif
                    </td>
                    <td class="p-4 text-right flex justify-end gap-2">
                        <button onclick="document.getElementById('modal-edit-{{ $meja->id }}').classList.remove('hidden')" class="text-blue-500 hover:text-blue-700 font-bold text-sm bg-blue-50 px-3 py-1 rounded-lg">Edit / Reset</button>
                        
                        <form action="{{ route('admin.meja.destroy', $meja->id) }}" method="POST" onsubmit="return confirm('Yakin hapus meja ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-sm bg-red-50 px-3 py-1 rounded-lg">Hapus</button>
                        </form>
                    </td>
                </tr>

                <div id="modal-edit-{{ $meja->id }}" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4 backdrop-blur-sm">
                    <div class="bg-white w-full max-w-sm rounded-2xl p-6 animate-fade-in-up shadow-2xl">
                        <h2 class="text-xl font-bold mb-4 text-gray-800">✏️ Edit Meja</h2>
                        <form action="{{ route('admin.meja.update', $meja->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Nomor Meja</label>
                                    <input type="text" name="nomor_meja" value="{{ $meja->nomor_meja }}" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-orange-500 outline-none" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Kapasitas (Orang)</label>
                                    <input type="number" name="kapasitas" value="{{ $meja->kapasitas }}" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-orange-500 outline-none" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Status (Force Reset)</label>
                                    <select name="status" class="w-full border rounded-lg p-2 bg-white">
                                        <option value="available" {{ $meja->status == 'available' ? 'selected' : '' }}>Available (Kosong)</option>
                                        <option value="booking" {{ $meja->status == 'booking' ? 'selected' : '' }}>Booking (Dipakai)</option>
                                    </select>
                                    <p class="text-xs text-gray-400 mt-1">*Gunakan ini jika status meja nyangkut.</p>
                                </div>
                            </div>
                            <div class="flex gap-3 mt-6">
                                <button type="button" onclick="document.getElementById('modal-edit-{{ $meja->id }}').classList.add('hidden')" class="flex-1 py-2 border rounded-xl font-bold text-gray-600 hover:bg-gray-50 transition">Batal</button>
                                <button type="submit" class="flex-1 py-2 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-200">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
                @endforeach
            </tbody>
        </table>
    </div>

    <div id="modal-add" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4 backdrop-blur-sm">
        <div class="bg-white w-full max-w-sm rounded-2xl p-6 animate-fade-in-up shadow-2xl">
            <h2 class="text-xl font-bold mb-4 text-gray-800">➕ Tambah Meja Baru</h2>
            <form action="{{ route('admin.meja.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nomor Meja</label>
                        <input type="text" name="nomor_meja" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-orange-500 outline-none" required placeholder="Contoh: 12 atau A1">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Kapasitas (Orang)</label>
                        <input type="number" name="kapasitas" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-orange-500 outline-none" required value="4">
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('modal-add').classList.add('hidden')" class="flex-1 py-2 border rounded-xl font-bold text-gray-600 hover:bg-gray-50 transition">Batal</button>
                    <button type="submit" class="flex-1 py-2 bg-orange-600 text-white rounded-xl font-bold hover:bg-orange-700 transition shadow-lg shadow-orange-200">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection