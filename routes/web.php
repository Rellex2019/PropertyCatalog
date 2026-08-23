<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\QRController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [PropertyController::class, 'index']);

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



Route::get('/catalog', [PropertyController::class, 'index'])->name('catalog.index');
Route::get('/catalog/{property}', [PropertyController::class, 'show'])->name('catalog.show');

Route::post('/qr/generate', [QRController::class, 'generate'])->name('qr.generate');
Route::get('/qr/download', [QRController::class, 'download'])->name('qr.download');

require __DIR__.'/auth.php';
