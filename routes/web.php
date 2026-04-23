<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\menuController;
use App\Http\Controllers\orderController;
use App\Http\Controllers\paymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('menus', menuController::class);

    Route::resource('orders', orderController::class)->except(['create', 'edit']);
    Route::patch('/orders/{order}/status', [orderController::class, 'updateStatus'])->name('orders.update-status');

    Route::resource('payments', paymentController::class)->only(['index', 'store']);
    Route::get('/orders/{order}/pay', [paymentController::class, 'create'])->name('payments.create');
});

require __DIR__.'/auth.php';