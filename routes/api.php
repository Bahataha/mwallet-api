<?php

use Illuminate\Http\Request;
use App\Http\Controllers\CloudController;
use App\Http\Controllers\VeriLiveController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('test', [CloudController::class, 'index']);
Route::post('verilive', [VeriLiveController::class, 'index']);
Route::get('testIIN/{index}', [CloudController::class, 'testIIN']);