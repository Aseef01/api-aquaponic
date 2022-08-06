<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\TreeController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\DeviceController;

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

Route::post('devices/create', [DeviceController::class, 'createDevice']);
Route::get('devices/read', [DeviceController::class, 'getAllDevice']);
Route::get('devices', [DeviceController::class, 'getAllInput']);
Route::post('devices', [DeviceController::class, 'createInputs']);
Route::get('streams', [DeviceController::class, 'getAllDataSensor']);
Route::post('streams', [DeviceController::class, 'insertSensor']);

Route::post('streams/click', [DeviceController::class, 'clickButton']);
Route::post('streams/edit', [DeviceController::class, 'changeButtonSetup']);

Route::post('users', [UserController::class, 'register']);
Route::post('users/login', [UserController::class, 'login']);
Route::post('users/edit', [UserController::class, 'editDataUser']);

Route::get('tree', [TreeController::class, 'getAllDataTree']);
Route::post('tree', [TreeController::class, 'insertTree']);
Route::get('tree/history', [TreeController::class, 'getTreeHistory']);
Route::post('tree/history', [TreeController::class, 'insertTreeHistory']);
Route::post('tree/history/edit', [TreeController::class, 'updateTreeHistory']);