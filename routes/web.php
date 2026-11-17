<?php

use App\Http\Controllers\BarangController;
use App\Http\Controllers\TabelViewController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'beranda');

Route::get('/barang', [BarangController::class, 'index']);
Route::post('/barang', [BarangController::class, 'store']);
Route::put('/barang/{id}', [BarangController::class, 'update']);
Route::patch('/barang/{id}/status', [BarangController::class, 'toggle']);

Route::get('/{nama}', [TabelViewController::class, 'show'])
    ->where('nama', 'role|satuan|user|vendor|margin|pengadaan|kartu-stok');
