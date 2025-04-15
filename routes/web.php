<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Rute Login & Logout
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute yang membutuhkan login
Route::middleware(['isLogin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    // Rute hanya untuk Admin
    Route::middleware('role:admin')->group(function () {
        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('users.index');
            Route::get('/create', [UserController::class, 'create'])->name('users.create');
            Route::post('/store', [UserController::class, 'store'])->name('users.store');
            Route::get('/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
            Route::put('/{id}', [UserController::class, 'update'])->name('users.update');
            Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        });
    });

    // Rute untuk Admin & Petugas
    Route::middleware('role:admin,petugas')->group(function () {
        Route::prefix('products')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('products.index');
            Route::get('/create', [ProductController::class, 'create'])->name('products.create');
            Route::post('/store', [ProductController::class, 'store'])->name('products.store');
            Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
            Route::put('/{product}', [ProductController::class, 'update'])->name('products.update');
            Route::delete('/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
        });

        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('orders.index');
            Route::get('/create', [OrderController::class, 'create'])->name('orders.create');
            Route::post('/orders/store', [OrderController::class, 'store'])->name('orders.store');
            Route::get('/member', [OrderController::class, 'memberPage'])->name('orders.member');
            Route::post('/members/save', [OrderController::class, 'save'])->name('members.save');

            // ✅ Ubah jadi GET dan sesuaikan URL
            Route::get('/create/summary', [OrderController::class, 'checkout'])->name('orders.checkout');

            // Route::post('/checkout/process', [OrderController::class, 'processCheckout'])->name('checkout.process');
            // Route::post('/checkout/process', [OrderController::class, 'processCheckout'])->name('orders.processCheckout');

            // Route::get('/{order}/download', [OrderController::class, 'downloadReceipt'])->name('orders.downloadReceipt');
            Route::get('/orders/search', [OrderController::class, 'search'])->name('orders.search');
            Route::get('/export', [OrderController::class, 'export'])->name('orders.export');
            Route::get('/orders/detail-print/{id}', [OrderController::class, 'detailPrint'])->name('orders.detailPrint');
            // Route::get('/orders/member', [OrderController::class, 'member'])->name('orders.member');
            Route::post('/orders/member/store', [OrderController::class, 'toMemberPage'])->name('orders.member.store');
            Route::post('/orders/store', [OrderController::class, 'store'])->name('orders.store');
            // Route::post('/orders/member/store', [OrderController::class, 'toMemberPage'])->name('orders.member.store');
            Route::get('/orders/{id}', [OrderController::class, 'show']);
            Route::get('/struk/{id}', [OrderController::class, 'cetakStruk'])->name('orders.struk');

        });
    });
    Route::post('/check-member-phone', [OrderController::class, 'checkPhone'])->name('members.checkPhone');
});
