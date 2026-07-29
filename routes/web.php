<?php

use App\Http\Controllers\Admin\CategoryAdminController;
use App\Http\Controllers\Admin\OrderAdminController;
use App\Http\Controllers\Admin\ProductAdminController;
use App\Http\Controllers\Admin\ShippingAdminController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmailVerificationPromptController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductControllers\CartItemController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\SessionsController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;
use Laravel\Paddle\Http\Controllers\WebhookController;

Route::get('/', [ProductController::class, 'home'])->name('home');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [SessionsController::class, 'destroy'])->name('logout');

    Route::get('/email/verify', EmailVerificationPromptController::class)->name('verification.notice');

    Route::post('/email/verification-notification', [
        EmailVerificationPromptController::class, 'sendEmailVerificaitonLink',
    ])->middleware(['throttle:6,1'])->name('verification.send');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill(); // Changes email_verified_at from NULL to current time

        return redirect()->intended(route('home'))->with('success', 'Email verified successfully!');
    })->middleware(['signed', 'throttle:3,1'])->name('verification.verify');

    // Orders Management
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('order.show');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // Checkout Flow
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

});

Route::post('/paddle/webhook', WebhookController::class);

// Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'verified'])->group(function () {
//     Route::get('/', DashboardController::class)->name('dashboard');

//     // Orders — no create (orders come from customers)
//     Route::resource('orders', OrderAdminController::class)
//         ->only(['index', 'show', 'update']);

//     // Products — full CRUD
//     Route::resource('products', ProductAdminController::class);
//     Route::delete('products/variants/{variant}', [ProductAdminController::class, 'destroyVariant'])
//         ->name('products.variants.destroy');
//     Route::delete('products/images/{image}', [ProductAdminController::class, 'destroyImage'])
//         ->name('products.images.destroy');

//     // Categories — tree management
//     Route::resource('categories', CategoryAdminController::class)
//         ->only(['index', 'store', 'update', 'destroy'])
//         ->parameters(['categories' => 'adminCategory']);
//     Route::patch('categories/{adminCategory}/toggle', [CategoryAdminController::class, 'toggle'])
//         ->name('categories.toggle');

//     // Users
//     Route::resource('users', UserAdminController::class)
//         ->only(['index', 'show', 'update', 'destroy']);

//     Route::patch('users/{user}/role', [UserAdminController::class, 'updateRole'])
//         ->name('users.role');

//     // Shipping methods
//     Route::resource('shipping', ShippingAdminController::class)
//         ->only(['index', 'store', 'update', 'destroy']);
// });

Route::group(['middleware' => 'guest'], function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');
    // Route::post('/categories/{category}', [ProductController::class, 'byCategory']);

    Route::get('/login', [SessionsController::class, 'create'])->name('login');
    Route::post('/login', [SessionsController::class, 'store'])->name('login.store');

    Route::get('/forgot-password', [PasswordResetController::class, 'create'])
        ->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'store'])
        ->middleware('throttle:3,1')
        ->name('password.email');

    Route::get('/reset-password/{token}', [PasswordResetController::class, 'edit'])
        ->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])
        ->name('password.update');

});

Route::get('/cart', [CartItemController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartItemController::class, 'store'])->name('cart.store');
Route::patch('/cart/{cartItem}', [CartItemController::class, 'update'])->name('cart.update');
Route::delete('/cart/{cartItem}', [CartItemController::class, 'destroy'])->name('cart.destroy');
Route::delete('/cart', [CartItemController::class, 'clear'])->name('cart.clear');

Route::prefix('{gender}')
    ->where(['gender' => '[a-z][a-z0-9-]*'])
    ->name('gender.')
    ->group(function () {

        Route::get('/', [ProductController::class, 'genderIndex'])
            ->name('index');

        Route::get('/{category}', [ProductController::class, 'categoryShow'])
            ->name('category.show');

        Route::get('/{category}/{subcategory}', [ProductController::class, 'subcategoryShow'])
            ->name('subcategory.show');

        Route::get('/{category}/{subcategory}/{product}', [ProductController::class, 'productShow'])
            ->name('product.show');
    });
