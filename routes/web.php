<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductController;

Route::get('/', function () {
    return view('welcome');
});

// user routes
Route::middleware(['auth', 'verified'])->group(function () {


    // Dashboard User
    Route::get('/dashboard', function () {
        return view('user.dashboard');
    })->name('dashboard');

    // Profile User (default Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::resource('products', ProductController::class);

    Route::get('/checkout-monitor', function () {
        $orders = \App\Models\Monitor::with('user')->get(); 
        return view('admin.checkoutMonitor.index', compact('orders'));
    })->name('checkout-monitor.index');
});


require __DIR__.'/auth.php';

