<?php

use App\Http\Controllers\AuthenticationController;
use App\Models\BackgroundRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/logout', [AuthenticationController::class, "logoutToken"]);
Route::post('/token', [AuthenticationController::class, "token"]);
Route::get('/user-data', [AuthenticationController::class, "authUserData"]);
