@extends('layouts.main')

@section('title', 'Kelola Stok Harian')

@section('content')
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">📝 Kelola Stok Harian</h1>
            <p class="text-gray-500 text-sm mt-1">Input jumlah masakan yang baru matang</p>
        </div>
        
        <a href="{{ route('dapur.index') }}" class="flex items-center gap-2 bg-white text-gray-600 px-4 py-2 rounded-xl border hover:bg-gray-50 transition shadow-sm font-bold">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            Kembali ke Monitor
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r shadow-sm animate-fade-in">
        <p class="font-bold">Berhasil!</p>
        <p>{{ session('success') }}</p>
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-bold tracking-wider">
                <tr>
                    <th class="p-5 border-b">Menu</th>
                    <th class="p-5 border-b text-center w-40">Sisa Stok</th>
                    <th class="p-5 border-b text-center">Tambah Masakan Baru</th>
                    <th class="p-5 border-b text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($menus as $menu)
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-5">
                        <div class="flex items-center gap-4">
                            <div class="h-12 w-12 rounded-lg bg-gray-100 overflow-hidden border">
                                @if($menu->foto)
                                <img src="{{ asset('storage/' . $menu->foto) }}" class="h-full w-full object-cover">
                                @else
                                <div class="h-full w-full flex items-center justify-center text-xs text-gray-400">No IMG</div>
                                @endif
                            </div>
                            <div>
                                <div class="font-bold text-gray-800">{{ $menu->nama_menu }}</div>
                                <div class="text-xs text-gray-400">{{ $menu->kategori->nama_kategori ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    
                    <td class="p-5 text-center">
                        <span class="text-lg font-bold {{ $menu->stok_porsi <= 5 ? 'text-red-600' : 'text-gray-700' }}">
                            {{ $menu->stok_porsi }}
                        </span>
                        <div class="text-[10px] text-gray-400">Porsi Tersedia</div>
                    </td>
                    
                    <form action="{{ route('dapur.stok.update', $menu->id) }}" method="POST">
                        @csrf
                        <td class="p-5">
                            <div class="flex items-center justify-center gap-2">
                                <span class="text-gray-400 font-bold text-xl">+</span>
                                <input type="number" name="jumlah_porsi" placeholder="0" min="1" required
                                       class="w-24 text-center border-2 border-gray-200 rounded-lg p-2 font-bold focus:border-orange-500 focus:ring-0 outline-none transition placeholder-gray-300">
                            </div>
                            <div class="text-center text-[10px] text-gray-400 mt-1">Masukkan jumlah yang dimasak</div>
                        </td>
                        <td class="p-5 text-center">
                            <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition shadow-md active:scale-95 text-sm font-bold flex items-center gap-2 mx-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                Tambah
                            </button>
                        </td>
                    </form>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection