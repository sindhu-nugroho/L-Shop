<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\PaymentController;
use App\Models\product;


Route::get('/', function () {
    return view('welcome');
    
});

// user routes
Route::middleware(['auth', 'verified'])->group(function () {


    // Dashboard User
    Route::get('/dashboard', function () {
        $products = product::latest()->get();
        return view('user.dashboard', compact('products'));
    })->name('dashboard');

    Route::post('/checkout/{product}', [PaymentController::class, 'checkout'])
        ->name('checkout.store');

    Route::post('/payment/status', [PaymentController::class, 'updateStatus'])
        ->name('payment.status.update');

    // Profile User (default Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

Route::post('/payment/notification', [PaymentController::class, 'notification'])
    ->name('payment.notification');

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::resource('products', ProductController::class);

    Route::post('/checkout/{product}', [PaymentController::class, 'checkout'])
        ->name('checkout.store');

    Route::post('/payment/status', [PaymentController::class, 'updateStatus'])
        ->name('payment.status.update');

    Route::get('/checkout-monitor', function () {
        $orders = \App\Models\Monitor::with('user')->latest()->get();
        return view('admin.checkoutMonitor.index', compact('orders'));
    })->name('checkout-monitor.index'); 
    Route::post('/get-snap-token', [PaymentController::class, 'getSnapToken']);
});


require __DIR__.'/auth.php';

