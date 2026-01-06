<?php

use App\Http\Controllers\Public\PropertyController as PublicPropertyController;
use App\Http\Controllers\Admin\PropertyController as AdminPropertyController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [PublicPropertyController::class, 'index'])->name('public.properties.index');
Route::get('/properties/{property}', [PublicPropertyController::class, 'show'])->name('public.properties.show');
Route::post('/properties/search', [PublicPropertyController::class, 'search'])->name('public.properties.search');

// Admin routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/properties', [AdminPropertyController::class, 'index'])->name('properties.index');
    Route::post('/properties', [AdminPropertyController::class, 'store'])->name('properties.store');
    Route::get('/properties/{property}', [AdminPropertyController::class, 'show'])->name('properties.show');
    Route::put('/properties/{property}', [AdminPropertyController::class, 'update'])->name('properties.update');
    Route::delete('/properties/{property}', [AdminPropertyController::class, 'destroy'])->name('properties.destroy');
    Route::post('/properties/import-csv', [AdminPropertyController::class, 'importCsv'])->name('properties.import.csv');
});
