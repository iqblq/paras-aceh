<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SatkerController;
use App\Http\Controllers\TematikController;
use App\Http\Controllers\TkdController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes - PARAS 001 PRO (Kanwil DJPb Aceh / KPPN 001 Banda Aceh)
|--------------------------------------------------------------------------
*/

// 1. Dashboard Utama
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// 2. Realisasi Anggaran 311 Satker
Route::get('/realisasi', [SatkerController::class, 'index'])->name('satker.index');
Route::get('/realisasi/export', [SatkerController::class, 'exportCsv'])->name('satker.export');

// 3. Modul Tematik Pengawasan
Route::prefix('tematik')->name('tematik.')->group(function () {
    Route::get('/prioritas', [TematikController::class, 'prioritas'])->name('prioritas');
    Route::get('/prioritas/export', [TematikController::class, 'exportPrioritasCsv'])->name('prioritas.export');
    Route::get('/strategis', [TematikController::class, 'strategis'])->name('strategis');
    Route::get('/strategis/export', [TematikController::class, 'exportStrategisCsv'])->name('strategis.export');
    Route::get('/bencana', [TematikController::class, 'bencana'])->name('bencana');
    Route::get('/bencana/export', [TematikController::class, 'exportBencanaCsv'])->name('bencana.export');
    Route::get('/jalan', [TematikController::class, 'jalan'])->name('jalan');
    Route::get('/jalan/export', [TematikController::class, 'exportJalanCsv'])->name('jalan.export');
    Route::get('/bansos', [TematikController::class, 'bansos'])->name('bansos');
    Route::get('/bansos/export', [TematikController::class, 'exportBansosCsv'])->name('bansos.export');
});

// 4. Transfer Ke Daerah (TKD Aceh)
Route::get('/tkd', [TkdController::class, 'index'])->name('tkd.index');
Route::get('/tkd/export', [TkdController::class, 'exportCsv'])->name('tkd.export');

// 5. REST API Endpoints untuk Filter Dinamis & Auth
Route::prefix('api')->name('api.')->group(function () {
    Route::get('/filter-bun', [ApiController::class, 'filterBunWilayah'])->name('filter.bun');
    Route::post('/admin/login', [ApiController::class, 'verifyAdmin'])->name('admin.login');
});

// 6. Admin Panel Kelola & Sinkronisasi Dataset SINTESA
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/sync', [AdminController::class, 'index'])->name('sync');
    Route::post('/sync/process', [AdminController::class, 'processSync'])->name('sync.process');
    Route::post('/sync/upload', [AdminController::class, 'uploadCsv'])->name('sync.upload');
});
