@extends('layouts.admin')

@section('content')
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Dashboard Overview</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="text-gray-400 text-xs font-bold uppercase mb-2">Total Pendapatan</div>
            <div class="text-2xl font-bold text-green-600">Rp {{ number_format($total_pendapatan) }}</div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="text-gray-400 text-xs font-bold uppercase mb-2">Total Orderan</div>
            <div class="text-2xl font-bold text-gray-800">{{ $total_order }}</div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="text-gray-400 text-xs font-bold uppercase mb-2">Total Menu</div>
            <div class="text-2xl font-bold text-orange-600">{{ $total_menu }}</div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="text-gray-400 text-xs font-bold uppercase mb-2">Total Pegawai</div>
            <div class="text-2xl font-bold text-blue-600">{{ $total_pegawai }}</div>
        </div>
    </div>
@endsection