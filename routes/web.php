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

/*
|--------------------------------------------------------------------------
| Default Dashboard (Laravel Breeze)
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return view('dashboard');
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
    });

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
