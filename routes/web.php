<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\Admin\AdminAssetController;

/*
|--------------------------------------------------------------------------
| Public Asset Routes
|--------------------------------------------------------------------------
| Anyone can view approved assets
*/

Route::get('/', [AssetController::class, 'index'])
    ->name('home');

Route::get('/assets/{slug}', [AssetController::class, 'show'])
    ->name('assets.show');

Route::get('/assets/{asset}/download', [AssetController::class, 'download'])
    ->name('assets.download');

/*
|--------------------------------------------------------------------------
| Default Dashboard (Laravel Breeze)
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    $assets = auth()->user()->assets()->latest()->get();

    return view('dashboard', compact('assets'));
})->middleware(['auth', 'verified'])
  ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Profile (Breeze default)
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    // Asset Upload
    Route::get('/upload', [AssetController::class, 'create'])
        ->name('assets.create');

    Route::post('/upload', [AssetController::class, 'store'])
        ->name('assets.store');

    Route::get('/assets/{asset}/edit', [AssetController::class, 'edit'])
        ->name('assets.edit');

    Route::put('/assets/{asset}', [AssetController::class, 'update'])
        ->name('assets.update');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| Only logged-in admins can access
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->group(function () {

        Route::get('/assets', [AdminAssetController::class, 'index'])
            ->name('admin.assets.index');

        Route::post('/assets/{id}/approve', [AdminAssetController::class, 'approve'])
            ->name('admin.assets.approve');

        Route::post('/assets/{id}/reject', [AdminAssetController::class, 'reject'])
            ->name('admin.assets.reject');

        Route::delete('/assets/{id}', [AdminAssetController::class, 'destroy'])
            ->name('admin.assets.destroy');
    });

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
