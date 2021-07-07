<?php

use App\Http\Controllers\VehicleController;
use App\Http\Controllers\ParkingLotController;
use App\Http\Controllers\VehicleTypeController;
use App\Http\Controllers\CardController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('v1/parking_lots/{parking_lot}/parking_lot_spaces', [ParkingLotController::class, 'parkingLotSpace']);
Route::put('v1/parking_lots/{parking_lot}/vehicles/{vehicle}/exit_parking', [VehicleController::class, 'exitParking']);
Route::get('v1/parking_lots/{parking_lot}/fetch_current_vehicle_info', [VehicleController::class, 'fetchCurrentVehicleInfo']);

Route::apiResources([
    'v1/parking_lots' => ParkingLotController::class,
    'v1/parking_lots/{parking_lot}/cards' => CardController::class,
    'v1/parking_lots/{parking_lot}/vehicles' => VehicleController::class,
    'v1/parking_lots/{parking_lot}/vehicle_types' => VehicleTypeController::class,
]);

