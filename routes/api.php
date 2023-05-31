<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\SupplierController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/auth/register', [AuthController::class, 'createUser']);
Route::post('/auth/login', [AuthController::class, 'loginUser']);
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/logout', [AuthController::class, 'logout']);

    // categories
    Route::get('categories', [CategoryController::class, 'index'])->name('categories');
    Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('categories/{item}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('categories/{item}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{item}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // products
    Route::get('products', [ProductController::class, 'index'])->name('products');
    Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('products', [ProductController::class, 'store'])->name('products.store');
    Route::get('products/{item}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('products/{item}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('products/{item}', [ProductController::class, 'destroy'])->name('products.destroy');

    // suppliers
    Route::get('suppliers', [SupplierController::class, 'index'])->name('suppliers');
    Route::get('suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
    Route::post('suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::get('suppliers/{item}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
    Route::put('suppliers/{item}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('suppliers/{item}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

    // purchases
    Route::get('purchases', [PurchaseController::class, 'index'])->name('purchases');
    Route::get('purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
    Route::post('purchases', [PurchaseController::class, 'store'])->name('purchases.store');
    Route::get('purchases/{item}/edit', [PurchaseController::class, 'edit'])->name('purchases.edit');
    Route::put('purchases/{item}', [PurchaseController::class, 'update'])->name('purchases.update');
    Route::delete('purchases/{item}', [PurchaseController::class, 'destroy'])->name('purchases.destroy');
    Route::get('product/list', [PurchaseController::class, 'productList'])->name('product.list');

});
