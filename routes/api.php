<?php

use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\RoleController;
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

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/forgot-password', [AuthController::class, 'forgot']);
Route::get('/forgot-password/{token}', [AuthController::class, 'forgot_password'])->name('password.reset');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/reset-password/{id}', [AuthController::class, 'reset']);

    Route::get('/users', [RoleController::class, 'showUsers']);

    Route::post('/add-user', [RoleController::class, 'addUser']);
    Route::put('/update-user/{id}', [RoleController::class, 'update']);
    Route::delete('/delete-user/{id}', [RoleController::class, 'delete']);

    Route::put('/change/{id}', [RoleController::class, 'changeAkses']);
});
