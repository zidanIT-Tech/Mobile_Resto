<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DapurController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

// 1. ROUTE PUBLIK (Order Menu - Tanpa Login)
Route::get('/', [OrderController::class, 'index'])->name('order.index');
Route::post('/order/simpan', [OrderController::class, 'store'])->name('order.simpan');


// 2. ROUTE AUTH (Login & Logout)
Route::get('/login', [AuthController::class, 'index'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.proses');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// 3. ROUTE PROTECTED (Harus Login)
Route::middleware(['auth'])->group(function () {

    // GROUP KASIR (Hanya Admin & Kasir)
    Route::middleware(['role:admin,kasir'])->group(function () {
        Route::get('/kasir', [KasirController::class, 'index'])->name('kasir.index');
        Route::get('/kasir/{id}/detail', [KasirController::class, 'detail'])->name('kasir.detail');
        Route::post('/kasir/{id}/bayar', [KasirController::class, 'bayar'])->name('kasir.bayar');
    });

    // GROUP DAPUR (Hanya Admin & Chef)
    Route::middleware(['role:admin,chef'])->group(function () {
        Route::get('/dapur', [DapurController::class, 'index'])->name('dapur.index');
        Route::post('/dapur/{id}/selesai', [DapurController::class, 'selesai'])->name('dapur.selesai');
        
        // --- TAMBAHAN BARU: KELOLA STOK ---
        Route::get('/dapur/stok', [DapurController::class, 'stok'])->name('dapur.stok');
        Route::post('/dapur/stok/{id}', [DapurController::class, 'updateStok'])->name('dapur.stok.update');
    });

    // GROUP ADMIN (Hanya Admin)
    Route::middleware(['role:admin'])->group(function () {
        
        // Dashboard
        Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
        
        // Kelola Pegawai
        Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
        Route::post('/admin/users', [AdminController::class, 'userStore'])->name('admin.users.store');
        Route::delete('/admin/users/{id}', [AdminController::class, 'userDestroy'])->name('admin.users.destroy');
        Route::put('/admin/users/{id}', [AdminController::class, 'userUpdate'])->name('admin.users.update');

        // KELOLA KATEGORI
        Route::get('/admin/kategori', [AdminController::class, 'kategori'])->name('admin.kategori');
        Route::post('/admin/kategori', [AdminController::class, 'kategoriStore'])->name('admin.kategori.store');
        Route::put('/admin/kategori/{id}', [AdminController::class, 'kategoriUpdate'])->name('admin.kategori.update');
        Route::delete('/admin/kategori/{id}', [AdminController::class, 'kategoriDestroy'])->name('admin.kategori.destroy');

        // KELOLA MENU
        Route::get('/admin/menu', [AdminController::class, 'menu'])->name('admin.menu');
        Route::post('/admin/menu', [AdminController::class, 'menuStore'])->name('admin.menu.store');
        Route::put('/admin/menu/{id}', [AdminController::class, 'menuUpdate'])->name('admin.menu.update');
        Route::delete('/admin/menu/{id}', [AdminController::class, 'menuDestroy'])->name('admin.menu.destroy');

        // KELOLA MEJA
        Route::get('/admin/meja', [AdminController::class, 'meja'])->name('admin.meja');
        Route::post('/admin/meja', [AdminController::class, 'mejaStore'])->name('admin.meja.store');
        Route::put('/admin/meja/{id}', [AdminController::class, 'mejaUpdate'])->name('admin.meja.update');
        Route::delete('/admin/meja/{id}', [AdminController::class, 'mejaDestroy'])->name('admin.meja.destroy');
    });

});