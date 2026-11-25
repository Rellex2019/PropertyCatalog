<?php

use App\Http\Controllers\Admin\PropertyController as AdminPropertyController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth', 'role:manager,admin,main_admin'])->prefix('admin')->name('admin.')->group(function () {
    // Главная админки
    Route::get('/', function () {
        return Inertia::render('Admin/Dashboard',
        );
    })->name('dashboard');

    // Управление недвижимостью
    Route::resource('properties', AdminPropertyController::class)->except(['show']);
    Route::patch('properties/{property}/toggle-active', [AdminPropertyController::class, 'toggleActive'])->name('properties.toggle-active');
    Route::patch('properties/{property}/toggle-featured', [AdminPropertyController::class, 'toggleFeatured'])->name('properties.toggle-featured');

    Route::middleware('role:main_admin')->group(function () {
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    });
});
