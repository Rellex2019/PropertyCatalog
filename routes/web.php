<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\QrController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [PropertyController::class, 'index'])->name('main');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



Route::get('/catalog', [PropertyController::class, 'index'])->name('catalog.index');
Route::get('/catalog/{property}', [PropertyController::class, 'show'])->name('catalog.show');

Route::post('/qr/generate', [QrController::class, 'generate'])->name('qr.generate');
Route::get('/qr/download', [QrController::class, 'download'])->name('qr.download');
Route::post('/qr/download-with-logo', [QrController::class, 'downloadWithLogo'])->name('qr.download-with-logo');
Route::get('/qr/inline', [QrController::class, 'inline'])->name('qr.inline');

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';

