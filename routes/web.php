<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RestaurantController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::redirect('/', '/dashboard');

Route::get('/dashboard', [RestaurantController::class, 'index'])->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/orders', [RestaurantController::class, 'order'])->name('orders.store');
    Route::patch('/orders/{order}/status', [RestaurantController::class, 'status'])->name('orders.status');
    Route::patch('/orders/{order}/pay', [RestaurantController::class, 'pay'])->name('orders.pay');
    Route::post('/menu-items', [RestaurantController::class, 'menu'])->name('menu.store');
    Route::post('/inventory', [RestaurantController::class, 'inventory'])->name('inventory.store');
    Route::patch('/inventory/{item}', [RestaurantController::class, 'stock'])->name('inventory.stock');
    Route::post('/expenses', [RestaurantController::class, 'expense'])->name('expenses.store');
    Route::post('/customers', [RestaurantController::class, 'customer'])->name('customers.store');
});

require __DIR__.'/auth.php';
