@extends('layouts.main')

@section('title', 'Monitor Dapur')

@section('content')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">👨‍🍳 Monitor Dapur</h1>
        <p class="text-gray-500 text-sm mt-1">Pantau pesanan masuk secara real-time</p>
    </div>

    <div class="flex items-center gap-3">
        <a href="{{ route('dapur.stok') }}" class="flex items-center gap-2 bg-orange-100 text-orange-700 px-4 py-2 rounded-xl border border-orange-200 hover:bg-orange-200 transition font-bold shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
            Kelola Stok
        </a>

        <div class="flex items-center gap-2 text-sm text-gray-600 bg-white px-3 py-1.5 rounded-full shadow-sm border">
            <span class="relative flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
            </span>
            Live (30s)
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
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 pb-20">
    @forelse($orderans as $order)
    @php
    // Gunakan 'detailOrderans' bukan 'details'
    $firstItem = $order->detailOrderans->first();
    $isTakeAway = $firstItem && $firstItem->metode_pesanan == 'takeaway';
    $catatan = $firstItem->catatan ?? null;
    @endphp

    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden flex flex-col h-full animate-fade-in hover:shadow-lg transition">

        <div class="p-4 border-b flex justify-between items-start bg-gray-50">
            <div>
                @if($isTakeAway)
                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-purple-100 text-purple-700 border border-purple-200 mb-2">
                    🛍️ TAKE AWAY
                </span>
                @else
                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-green-100 text-green-700 border border-green-200 mb-2">
                    🍽️ MEJA {{ $order->meja->nomor_meja ?? '-' }}
                </span>
                @endif
                <h2 class="font-bold text-gray-800 text-lg leading-tight">{{ $order->nama_konsumen }}</h2>
            </div>
            <div class="text-right">
                <div class="text-xs font-mono text-gray-500 bg-white px-2 py-1 rounded border shadow-sm">
                    {{ $order->created_at->format('H:i') }}
                </div>
                <div class="text-[10px] text-gray-400 mt-1">
                    #{{ $order->id }}
                </div>
            </div>
        </div>

        <div class="p-4 flex-1 overflow-y-auto max-h-80">
            <ul class="space-y-3">
                @foreach($order->detailOrderans as $detail)
                <li class="flex justify-between items-start text-sm pb-2 border-b border-dashed border-gray-200 last:border-0 last:pb-0">
                    <span class="text-gray-700 font-medium leading-snug w-3/4">
                        {{ $detail->menu->nama_menu }}
                    </span>
                    <span class="font-bold text-orange-600 bg-orange-50 px-2 py-0.5 rounded text-xs whitespace-nowrap">
                        x {{ $detail->jumlah }}
                    </span>
                </li>
                @endforeach
            </ul>

            @if($catatan)
            <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                <p class="text-[10px] uppercase font-bold text-yellow-600 mb-1">📝 Catatan Khusus:</p>
                <p class="text-xs text-gray-700 italic">"{{ $catatan }}"</p>
            </div>
            @endif
        </div>

        <div class="p-4 border-t bg-gray-50 mt-auto">
            <form action="{{ route('dapur.selesai', $order->id) }}" method="POST" onsubmit="return confirm('Pesanan sudah selesai dimasak?')">
                @csrf
                <button type="submit" class="w-full bg-white border border-green-500 text-green-600 hover:bg-green-500 hover:text-white py-2.5 rounded-xl font-bold transition shadow-sm active:scale-95 flex items-center justify-center gap-2 group">
                    <span>Selesai Masak</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="col-span-full flex flex-col items-center justify-center py-20 text-center">
        <div class="bg-gray-100 p-6 rounded-full mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <h3 class="text-xl font-bold text-gray-600">Dapur Sepi...</h3>
        <p class="text-gray-400">Belum ada pesanan yang masuk.</p>
    </div>
    @endforelse
</div>
@endsection

@push('scripts')
<script>
    setInterval(function() {
        window.location.reload();
    }, 30000);
</script>
@endpush