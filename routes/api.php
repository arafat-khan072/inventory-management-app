<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
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

});
