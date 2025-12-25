<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TripayController;
use App\Http\Controllers\TripayCallbackController;

// Rute untuk proses memilih metode & bayar
Route::get('/payment/{trx_id}', [HomeController::class, 'paymentConfirm'])->name('payment.confirm');
Route::post('/payment/{id}/checkout', [HomeController::class, 'checkout'])->name('payment.checkout');
Route::get('/payment/detail/{trx_id}', [HomeController::class, 'paymentDetail'])->name('payment.detail');

Route::post('/api/callback', [TripayCallbackController::class, 'handle']);

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/register', [HomeController::class, 'storeRegistration'])->name('register.store');
Route::get('/payment/{id}', [HomeController::class, 'paymentConfirm'])->name('payment.confirm');
Route::get('/success/{trx_id}', [HomeController::class, 'successPage'])->name('payment.success');

Route::get('/download-qris', [HomeController::class, 'downloadQris'])->name('qris.download');

Route::get('/admin/login', [AdminController::class, 'login'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'authenticate'])->name('admin.login.post');
Route::get('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');
Route::post('/admin/bulk-store/{season_id}', [AdminController::class, 'bulkStore'])->name('admin.bulk.store');
Route::get('/admin/team/delete/{id}', [AdminController::class, 'deleteTeam'])->name('admin.team.delete');
Route::get('/admin/team/delete-all/{season_id}', [AdminController::class, 'deleteAllTeams'])->name('admin.team.deleteAll');
Route::post('/admin/seasons/store', [AdminController::class, 'storeSeason'])->name('admin.seasons.store');
Route::post('/admin/seasons/update/{id}', [AdminController::class, 'updateSeason'])->name('admin.seasons.update');
Route::get('/admin/seasons/delete/{id}', [AdminController::class, 'deleteSeason'])->name('admin.seasons.delete');
Route::post('/admin/team/update/{id}', [AdminController::class, 'updateTeam'])->name('admin.team.update');

Route::prefix('admin')->group(function () {
    Route::get('/seasons', [AdminController::class, 'seasons'])->name('admin.seasons');
    Route::get('/dashboard/{season_id}', [AdminController::class, 'dashboard'])->name('admin.dashboard');
});
