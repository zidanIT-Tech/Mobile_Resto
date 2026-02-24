@extends('layouts.main')

@section('title', 'Detail Pembayaran')

@section('content')
<style>
    @media print {
        body * { visibility: hidden; }
        #area-struk, #area-struk * { visibility: visible; }
        #area-struk { position: absolute; left: 0; top: 0; width: 100%; margin: 0; padding: 0; box-shadow: none; }
        .no-print { display: none !important; }
        body { background-color: white; } 
    }
</style>

<div class="max-w-4xl mx-auto flex flex-col md:flex-row gap-8 items-start">
    
    <div class="bg-white p-6 md:p-8 rounded-xl shadow-lg w-full md:w-1/2 border border-gray-100" id="area-struk">
        
        <div class="text-center mb-6 border-b-2 border-dashed border-gray-200 pb-6">
            <h2 class="text-3xl font-bold uppercase tracking-widest text-gray-800">RestoKu</h2>
            <p class="text-sm text-gray-500 mt-1">Jl. Orang Sultan No. 77, Samarinda</p>
            <p class="text-sm text-gray-500">Telp: 0823-5014-7795</p>
        </div>

        <div class="flex justify-between text-sm mb-6 text-gray-600">
            <div class="space-y-1">
                <p>Order ID: <span class="font-mono font-bold text-gray-800">#{{ $order->id }}</span></p>
                <p>Pemesan: <span class="font-bold text-gray-800">{{ $order->nama_konsumen }}</span></p>
                @if($order->id_meja)
                    <p class="font-bold text-blue-600">MEJA {{ $order->meja->nomor_meja }}</p>
                @else
                    <p class="font-bold text-purple-600">TAKE AWAY</p>
                @endif
            </div>
            <div class="text-right space-y-1">
                <p>{{ $order->created_at->format('d/m/Y') }}</p>
                <p>{{ $order->created_at->format('H:i') }} WIB</p>
                
                <p class="uppercase text-xs font-bold bg-gray-100 px-2 py-0.5 rounded inline-block mt-1 border">
                    {{ $order->metode_pembayaran ?? 'Tunai' }}
                </p>
            </div>
        </div>

        <div class="mb-4 border-b-2 border-dashed border-gray-200 pb-4">
            <table class="w-full text-sm">
                @foreach($order->detailOrderans as $detail)
                <tr>
                    <td class="py-1">
                        <div class="font-medium text-gray-800">{{ $detail->menu->nama_menu }}</div>
                        </td>
                    <td class="py-1 text-center text-gray-500">x{{ $detail->jumlah }}</td>
                    <td class="py-1 text-right font-mono text-gray-800">{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </table>
        </div>

        @php
            // Ambil catatan dari item pertama (karena saat order, catatannya disimpan sama ke semua item)
            $catatanUmum = $order->detailOrderans->first()->catatan ?? null;
        @endphp

        @if($catatanUmum)
        <div class="mb-6 bg-gray-50 p-3 rounded-lg border border-dashed border-gray-300">
            <p class="text-[10px] font-bold text-gray-500 uppercase mb-1">Catatan Pesanan:</p>
            <p class="text-sm text-gray-800 italic">"{{ $catatanUmum }}"</p>
        </div>
        @else
        <div class="mb-6"></div> 
        @endif

        <div class="flex justify-between items-center mb-8">
            <span class="text-gray-600 font-bold text-lg">TOTAL</span>
            <span class="text-3xl font-bold text-gray-900">Rp {{ number_format($order->total_bayar, 0, ',', '.') }}</span>
        </div>

        <div class="text-center text-xs text-gray-400 mb-2">
            <p class="mb-1">Terima kasih atas kunjungan Anda!</p>
            <p>Password Wifi: <span class="font-mono font-bold text-gray-600">pesanmakandapatwifi</span></p>
        </div>
    </div>

    <div class="w-full md:w-1/2 space-y-6 no-print">
        
        @if($order->metode_pembayaran == 'cash')
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-700 mb-4 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                Hitung Kembalian
            </h3>
            
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Uang Diterima</label>
                <input type="number" id="uang-bayar" class="w-full border border-gray-300 rounded-lg p-3 text-lg font-mono focus:ring-2 focus:ring-blue-500 outline-none" placeholder="0" onkeyup="hitungKembalian()">
                <div class="flex gap-2 mt-2 overflow-x-auto pb-1">
                    <button onclick="setUang({{ $order->total_bayar }})" class="bg-gray-100 px-3 py-1 rounded text-xs font-bold hover:bg-gray-200">Pas</button>
                    <button onclick="setUang(50000)" class="bg-gray-100 px-3 py-1 rounded text-xs font-bold hover:bg-gray-200">50k</button>
                    <button onclick="setUang(100000)" class="bg-gray-100 px-3 py-1 rounded text-xs font-bold hover:bg-gray-200">100k</button>
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg flex justify-between items-center">
                <span class="text-gray-600 font-bold">Kembalian:</span>
                <span id="text-kembalian" class="text-xl font-bold font-mono text-gray-400">Rp 0</span>
            </div>
        </div>
        @else
        <div class="bg-blue-50 p-6 rounded-xl border border-blue-100 text-center">
            <p class="text-blue-800 font-bold mb-1">Pembayaran Non-Tunai</p>
            <p class="text-sm text-blue-600">Metode: <span class="uppercase font-bold">{{ $order->metode_pembayaran }}</span></p>
            <p class="text-xs text-blue-500 mt-2">Pastikan bukti transfer/QRIS valid sebelum konfirmasi.</p>
        </div>
        @endif

        <div class="space-y-3">
            <button onclick="window.print()" class="w-full bg-gray-800 text-white py-3 rounded-xl font-bold hover:bg-gray-900 flex items-center justify-center gap-2 transition shadow-lg shadow-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                Cetak Struk
            </button>
            
            <form action="{{ route('kasir.bayar', $order->id) }}" method="POST" onsubmit="return confirm('Konfirmasi pembayaran ini selesai?')">
                @csrf
                <button type="submit" class="w-full bg-green-600 text-white py-4 rounded-xl font-bold hover:bg-green-700 shadow-xl shadow-green-200 flex items-center justify-center gap-2 transition transform hover:-translate-y-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    KONFIRMASI LUNAS
                </button>
            </form>
            
            <a href="{{ route('kasir.index') }}" class="block text-center text-gray-400 hover:text-gray-600 font-bold text-sm py-2">
                &larr; Kembali ke Daftar Tagihan
            </a>
        </div>
    </div>
</div>

<script>
    const totalTagihan = {{ $order->total_bayar }};
    
    function hitungKembalian() {
        let uang = document.getElementById('uang-bayar').value;
        let kembalian = uang - totalTagihan;
        
        let elKembalian = document.getElementById('text-kembalian');
        
        if (uang === "") {
            elKembalian.innerText = "Rp 0";
            elKembalian.classList.add('text-gray-400');
            elKembalian.classList.remove('text-green-600', 'text-red-500');
            return;
        }

        if (kembalian >= 0) {
            elKembalian.innerText = "Rp " + kembalian.toLocaleString('id-ID');
            elKembalian.classList.remove('text-gray-400', 'text-red-500');
            elKembalian.classList.add('text-green-600');
        } else {
            elKembalian.innerText = "Kurang Rp " + Math.abs(kembalian).toLocaleString('id-ID');
            elKembalian.classList.remove('text-gray-400', 'text-green-600');
            elKembalian.classList.add('text-red-500');
        }
    }

    function setUang(nominal) {
        document.getElementById('uang-bayar').value = nominal;
        hitungKembalian();
    }
</script>
@endsection