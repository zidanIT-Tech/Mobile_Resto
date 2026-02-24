@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">👥 Kelola Pegawai</h1>
        
        <button onclick="document.getElementById('modal-add').classList.remove('hidden')" class="bg-orange-600 text-white px-4 py-2 rounded-xl font-bold shadow hover:bg-orange-700 transition flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" /></svg>
            Tambah Pegawai
        </button>
    </div>

    @if(session('success'))
    <div class="bg-green-100 text-green-700 p-3 rounded-lg mb-4 border border-green-200">{{ session('success') }}</div>
    @endif
    
    @if(session('error'))
    <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-4 border border-red-200">{{ session('error') }}</div>
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
                    <th class="p-4 text-sm text-gray-500">Nama</th>
                    <th class="p-4 text-sm text-gray-500">Email</th>
                    <th class="p-4 text-sm text-gray-500">Role</th>
                    <th class="p-4 text-sm text-gray-500 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="p-4 font-bold text-gray-700">{{ $user->username }}</td>
                    <td class="p-4 text-gray-600">{{ $user->email }}</td>
                    <td class="p-4">
                        @if($user->role == 'admin') <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-bold uppercase">Admin</span>
                        @elseif($user->role == 'kasir') <span class="bg-blue-100 text-blue-600 px-2 py-1 rounded text-xs font-bold uppercase">Kasir</span>
                        @elseif($user->role == 'chef') <span class="bg-orange-100 text-orange-600 px-2 py-1 rounded text-xs font-bold uppercase">Chef</span>
                        @endif
                    </td>
                    <td class="p-4 text-right flex justify-end gap-2">
                        @if(auth()->user()->id != $user->id)
                            <button onclick="document.getElementById('modal-edit-{{ $user->id }}').classList.remove('hidden')" class="text-blue-500 hover:text-blue-700 font-bold text-sm bg-blue-50 px-3 py-1 rounded-lg">Edit</button>

                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('⚠️ PERINGATAN!\n\nApakah Anda yakin ingin menghapus pegawai ini?\nData yang dihapus tidak dapat dikembalikan.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-sm bg-red-50 px-3 py-1 rounded-lg">Hapus</button>
                            </form>
                        @else
                            <span class="text-gray-400 text-xs italic px-3 py-1">Akun Anda</span>
                        @endif
                    </td>
                </tr>

                <div id="modal-edit-{{ $user->id }}" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4 backdrop-blur-sm">
                    <div class="bg-white w-full max-w-md rounded-2xl p-6 animate-fade-in-up shadow-2xl">
                        <h2 class="text-xl font-bold mb-4 text-gray-800">✏️ Edit Pegawai</h2>
                        
                        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama Username</label>
                                    <input type="text" name="username" value="{{ $user->username }}" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-orange-500 outline-none" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Email</label>
                                    <input type="email" name="email" value="{{ $user->email }}" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-orange-500 outline-none" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Password Baru (Opsional)</label>
                                    <input type="password" name="password" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-orange-500 outline-none" placeholder="Isi jika ingin mengganti password">
                                    <p class="text-[10px] text-gray-400 mt-1">*Biarkan kosong jika tidak ingin mengganti password</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Role / Jabatan</label>
                                    <select name="role" class="w-full border rounded-lg p-2 bg-white focus:ring-2 focus:ring-orange-500 outline-none">
                                        <option value="kasir" {{ $user->role == 'kasir' ? 'selected' : '' }}>Kasir</option>
                                        <option value="chef" {{ $user->role == 'chef' ? 'selected' : '' }}>Chef (Koki)</option>
                                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                </div>
                            </div>
                            <div class="flex gap-3 mt-6">
                                <button type="button" onclick="document.getElementById('modal-edit-{{ $user->id }}').classList.add('hidden')" class="flex-1 py-2 border rounded-xl font-bold text-gray-600 hover:bg-gray-50 transition">Batal</button>
                                <button type="submit" class="flex-1 py-2 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-200">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
                @endforeach
            </tbody>
        </table>
    </div>

    <div id="modal-add" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4 backdrop-blur-sm">
        <div class="bg-white w-full max-w-md rounded-2xl p-6 animate-fade-in-up shadow-2xl">
            <h2 class="text-xl font-bold mb-4 text-gray-800">➕ Tambah Pegawai Baru</h2>
            
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nama Username</label>
                        <input type="text" name="username" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-orange-500 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-orange-500 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Password</label>
                        <input type="password" name="password" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-orange-500 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Role / Jabatan</label>
                        <select name="role" class="w-full border rounded-lg p-2 bg-white focus:ring-2 focus:ring-orange-500 outline-none">
                            <option value="kasir">Kasir</option>
                            <option value="chef">Chef (Koki)</option>
                            <option value="admin">Admin</option>
                        </select>
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