<?php
use App\Http\Controllers\Admin\PropertyController as AdminPropertyController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Главная админки
    Route::get('/', function () {
        return Inertia::render('Admin/Dashboard',
        );
    })->name('dashboard');

    // Управление недвижимостью
    Route::resource('properties', AdminPropertyController::class)->except(['show']);
    Route::patch('properties/{property}/toggle-active', [AdminPropertyController::class, 'toggleActive'])->name('properties.toggle-active');
    Route::patch('properties/{property}/toggle-featured', [AdminPropertyController::class, 'toggleFeatured'])->name('properties.toggle-featured');
});
