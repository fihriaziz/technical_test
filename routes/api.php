<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\RoleController;
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

Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login')->middleware('guest')->name('login');
    Route::post('/register', 'register')->middleware('guest');
    Route::post('/forgot-password', 'forgot_password');
    Route::get('/reset-password/{token}', 'reset_token');
    Route::post('/reset-password', 'reset_password')->name('password.reset');
});

Route::middleware('auth:sanctum')->group(function () {

    Route::controller(AuthController::class)->group(function () {
        Route::get('/profile', 'me');
        Route::post('/logout',  'logout');
    });

    Route::controller(RoleController::class)->group(function () {
        Route::post('/add-user',  'addUser');
        Route::get('/users',  'showUsers');
        Route::put('/update-user/{id}',  'update');
        Route::delete('/delete-user/{id}',  'delete');

        Route::put('/change/{id}',  'changeAkses');
    });
});
