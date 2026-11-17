<?php

use App\Http\Controllers\BarangController;
use App\Http\Controllers\MarginController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\TabelViewController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PengadaanController;
use App\Http\Controllers\PenerimaanController;

Route::view('/', 'beranda');

// Barang
Route::get('/barang', [BarangController::class, 'index']);
Route::post('/barang', [BarangController::class, 'store']);
Route::put('/barang/{id}', [BarangController::class, 'update']);
Route::patch('/barang/{id}/status', [BarangController::class, 'toggle']);

// Vendor
Route::get('/vendor', [VendorController::class, 'index']);
Route::post('/vendor', [VendorController::class, 'store']);
Route::put('/vendor/{id}', [VendorController::class, 'update']);
Route::patch('/vendor/{id}/status', [VendorController::class, 'toggle']);

// Satuan
Route::get('/satuan', [SatuanController::class, 'index']);
Route::post('/satuan', [SatuanController::class, 'store']);
Route::put('/satuan/{id}', [SatuanController::class, 'update']);
Route::patch('/satuan/{id}/status', [SatuanController::class, 'toggle']);

// Margin
Route::get('/margin', [MarginController::class, 'index']);
Route::post('/margin', [MarginController::class, 'store']);
Route::put('/margin/{id}', [MarginController::class, 'update']);
Route::patch('/margin/{id}/status', [MarginController::class, 'toggle']);

// User
Route::get('/user', [UserController::class, 'index']);
Route::post('/user', [UserController::class, 'store']);
Route::put('/user/{id}', [UserController::class, 'update']);
Route::delete('/user/{id}', [UserController::class, 'destroy']);

// Penjualan
Route::get('/penjualan', [PenjualanController::class, 'index']);
Route::post('/penjualan', [PenjualanController::class, 'store']);

// Pengadaan (header + detail satu halaman)
Route::get('/pengadaan', [PengadaanController::class, 'index']);
Route::post('/pengadaan', [PengadaanController::class, 'store']);
Route::delete('/pengadaan/{id}', [PengadaanController::class, 'destroy']);

// Penerimaan
Route::get('/penerimaan', [PenerimaanController::class, 'index']);
Route::post('/penerimaan', [PenerimaanController::class, 'store']);
Route::patch('/penerimaan/{id}/tuntas', [PenerimaanController::class, 'tuntas']);

Route::get('/{nama}', [TabelViewController::class, 'show'])
    ->where('nama', 'role|rekap-vendor|kartu-stok');
