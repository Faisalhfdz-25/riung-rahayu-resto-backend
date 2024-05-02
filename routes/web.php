<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('pages.auth.login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('home', function () {
        return view('pages.dashboard');
    })->name('home');

    Route::resource('user', UserController::class)->only(['index', 'store', 'update', 'destroy'])->names('user');
    Route::resource('products', ProductController::class)->names('products');
    // Custom route for updating product image
    Route::put('products/{product}/change-image', [ProductController::class, 'updateImage'])->name('products.change-image');
});
