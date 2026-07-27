<?php

use App\Http\Controllers\Prototype\PrototypeDashboardController;
use App\Http\Controllers\Prototype\PrototypeMapOverlayAdminController;
use App\Http\Controllers\Prototype\PrototypeMapSectionController;
use App\Models\Prototype\PrototypeMapOverlay;
use App\Models\Prototype\PrototypeMapSection;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Prototype Map Overlay — isolated POC routes
|--------------------------------------------------------------------------
|
| These routes are for internal development and testing only. They must not
| be indexed by search engines and must remain separate from production
| Project / DHA Phase modules.
|
*/

Route::middleware(['prototype.noindex'])->prefix('prototype')->name('prototype.')->group(function () {
    Route::get('/', [PrototypeDashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/interactive-map', [PrototypeDashboardController::class, 'interactiveMap'])->name('interactive-map');
    Route::get('/interactive-map/{overlay}', [PrototypeDashboardController::class, 'interactiveMap'])->name('interactive-map.show');
});

Route::middleware(['admin', 'prototype.noindex'])->prefix('admin/prototype')->name('admin.prototype.')->group(function () {
    Route::get('/interactive-map', [PrototypeMapOverlayAdminController::class, 'index'])->name('interactive-map.index');

    Route::post('/interactive-map', [PrototypeMapOverlayAdminController::class, 'store'])->name('interactive-map.store');

    Route::prefix('interactive-map/{overlay}')->whereNumber('overlay')->group(function () {
        Route::patch('/', [PrototypeMapOverlayAdminController::class, 'update'])->name('interactive-map.update');
        Route::post('/upload', [PrototypeMapOverlayAdminController::class, 'upload'])->name('interactive-map.upload');
        Route::delete('/image', [PrototypeMapOverlayAdminController::class, 'deleteImage'])->name('interactive-map.delete-image');
        Route::delete('/', [PrototypeMapOverlayAdminController::class, 'destroy'])->name('interactive-map.destroy');
        Route::get('/config', [PrototypeMapOverlayAdminController::class, 'config'])->name('interactive-map.config');

        Route::get('/sections', [PrototypeMapSectionController::class, 'index'])->name('sections.index');
        Route::post('/sections', [PrototypeMapSectionController::class, 'store'])->name('sections.store');
        Route::patch('/sections/{section}', [PrototypeMapSectionController::class, 'update'])->name('sections.update');
        Route::delete('/sections/{section}', [PrototypeMapSectionController::class, 'destroy'])->name('sections.destroy');
    })->scopeBindings();
});

Route::bind('overlay', function (string $value) {
    return PrototypeMapOverlay::query()->findOrFail($value);
});

Route::bind('section', function (string $value) {
    return PrototypeMapSection::query()->findOrFail($value);
});
