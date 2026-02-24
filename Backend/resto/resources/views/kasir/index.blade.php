@extends('layouts.main')

@section('title', 'Kasir')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">💰 Kasir / Pembayaran</h1>
        <p class="text-gray-500 text-sm mt-1">Kelola pembayaran dan status meja</p>
    </div>

    <div class="flex flex-wrap items-center gap-4">
        
        <div class="flex items-center gap-2 text-xs font-bold text-green-600 bg-green-50 px-3 py-2 rounded-xl border border-green-100 shadow-sm">
            <span class="relative flex h-2 w-2">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
            </span>
            Live (30s)
        </div>

        <div class="bg-white px-4 py-2 rounded-xl border shadow-sm flex gap-4 text-sm">
            <div>
                <span class="text-gray-500 block text-xs">Belum Bayar</span>
                <span class="font-bold text-lg text-orange-600">{{ $orderans->count() }}</span>
            </div>
            <div class="border-l pl-4">
                <span class="text-gray-500 block text-xs">Tanggal</span>
                <span class="font-bold text-gray-800">{{ date('d M Y') }}</span>
            </div>
        </div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="bg-red-100 text-red-600 px-4 py-3 rounded-xl font-bold hover:bg-red-200 transition shadow-sm flex items-center gap-2" title="Keluar Sistem">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span class="hidden md:inline">Logout</span>
            </button>
        </form>
    </div>
</div>

@if(session('success'))
<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r shadow-sm animate-fade-in" role="alert">
    <p class="font-bold">Berhasil</p>
    <p>{{ session('success') }}</p>
</div>
@endif

<div class="mb-10">
    <h2 class="text-lg font-bold text-gray-700 mb-4 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" /></svg>
        Status Meja Restoran
    </h2>
    
    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3">
        @foreach($mejas as $meja)
        <div class="relative rounded-xl p-3 border {{ $meja->status == 'available' ? 'bg-white border-green-200 shadow-sm' : 'bg-red-50 border-red-200' }} flex flex-col items-center justify-center transition hover:shadow-md">
            
            <div class="mb-1">
                @if($meja->status == 'available')
                    <span class="text-2xl">🪑</span>
                @else
                    <span class="text-2xl">🍲</span>
                @endif
            </div>

            <span class="font-bold {{ $meja->status == 'available' ? 'text-gray-700' : 'text-red-600' }}">
                Meja {{ $meja->nomor_meja }}
            </span>

            <span class="text-[10px] uppercase font-bold mt-1 px-2 py-0.5 rounded-full {{ $meja->status == 'available' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                {{ $meja->status == 'available' ? 'Kosong' : 'Terisi' }}
            </span>

            <div class="absolute top-1 right-1 text-[9px] text-gray-400 font-mono">
                {{ $meja->kapasitas }}org
            </div>
        </div>
        @endforeach
    </div>
</div>

<h2 class="text-lg font-bold text-gray-700 mb-4 flex items-center gap-2 border-t pt-6">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
    Daftar Tagihan (Belum Lunas)
</h2>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 pb-20">
    @forelse($orderans as $order)
    @php
    // Logic Cek Tipe Pesanan
    $isTakeAway = is_null($order->id_meja);

    // Logic Cek Status Masakan (Cek apakah masih ada item yang processing)
    $pendingItems = $order->detailOrderans->where('status', 'processing')->count();
    $isCooking = $pendingItems > 0;
    @endphp

    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden flex flex-col hover:shadow-lg transition duration-300">

        <div class="p-5 border-b bg-gray-50 flex justify-between items-start">
            <div>
                @if($isTakeAway)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-purple-100 text-purple-700 border border-purple-200 mb-2">
                    🛍️ TAKE AWAY
                </span>
                @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-blue-100 text-blue-700 border border-blue-200 mb-2">
                    🍽️ MEJA {{ $order->meja->nomor_meja }}
                </span>
                @endif
                <h2 class="font-bold text-gray-800 text-lg leading-tight">{{ $order->nama_konsumen }}</h2>
                <p class="text-xs text-gray-400 mt-1">Order #{{ $order->id }} • {{ $order->created_at->format('H:i') }}</p>
            </div>
        </div>

        <div class="p-5 flex-1">
            <div class="mb-4">
                <p class="text-xs text-gray-500 mb-1">Status Dapur:</p>
                @if($isCooking)
                <div class="flex items-center gap-2 text-yellow-600 bg-yellow-50 px-3 py-2 rounded-lg border border-yellow-100">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-yellow-500"></span>
                    </span>
                    <span class="text-sm font-bold">Sedang Dimasak ({{ $pendingItems }} item)</span>
                </div>
                @else
                <div class="flex items-center gap-2 text-green-600 bg-green-50 px-3 py-2 rounded-lg border border-green-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm font-bold">Siap Saji / Selesai</span>
                </div>
                @endif
            </div>

            <div class="flex justify-between items-end border-t pt-4">
                <div>
                    <p class="text-xs text-gray-400">Total Tagihan</p>
                    <p class="text-xl font-bold text-orange-600">Rp {{ number_format($order->total_bayar, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="p-4 bg-gray-50 border-t">
            <a href="{{ route('kasir.detail', $order->id) }}" class="flex items-center justify-center w-full bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 transition shadow-sm active:scale-95">
                Proses Pembayaran
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </a>
        </div>

    </div>
    @empty
    <div class="col-span-full flex flex-col items-center justify-center py-20">
        <div class="bg-gray-100 p-6 rounded-full mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </div>
        <h3 class="text-xl font-bold text-gray-500">Semua Beres!</h3>
        <p class="text-gray-400">Tidak ada tagihan yang belum dibayar saat ini.</p>
    </div>
    @endforelse
</div>

<script>
    setTimeout(function(){
       window.location.reload();
    }, 30000);
</script>
@endsection