<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProfileController;


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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->middleware('is-token')->group(function() {
    Route::prefix("auth")->group(function() {
        Route::post('login', [LoginController::class, 'login'])->withoutMiddleware('is-token');
    });

    Route::get('category/all', [CategoryController::class, 'getall']);
    Route::resource('category', CategoryController::class);
    Route::resource('book', BookController::class);
    Route::post('book/update/{id}', [BookController::class, 'updateBook']);

    Route::resource('user', UserController::class)->middleware('is-admin');
    Route::post('user/update/{id}', [UserController::class, 'updateUser'])->middleware('is-admin');

    Route::get('profile', [ProfileController::class, 'index']);
    Route::post('profile/update', [ProfileController::class, 'updateProfile']);
    Route::delete('profile/avatar/delete', [ProfileController::class, 'deleteAvatar']);
});