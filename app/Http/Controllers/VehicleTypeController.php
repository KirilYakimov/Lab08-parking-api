<?php

namespace App\Http\Controllers;

use App\Http\Resources\VehicleTypeResource;
use App\Models\VehicleType;
use App\Models\ParkingLot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VehicleTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param  \App\Models\ParkingLot
     * @return \Illuminate\Http\Response
     */
    public function index(ParkingLot $parkingLot)
    {
        return response()->json([
            'success' => 1,
            'data' => VehicleTypeResource::collection($parkingLot->vehicleTypes)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, ParkingLot $parkingLot)
    {
        //Validate the input data
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string'],
            'category' => ['required', 'string'],
            'daily_rate' => ['required', 'numeric'],
            'night_rate' => ['required', 'numeric'],
            'parking_space' => ['required', 'numeric'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 1,
                'data' => $validator->errors()
            ], 400);
        }

        //Create a new parking lot and save it to the database
        $vehicleType = $parkingLot->vehicleTypes()->create([
            'name' => $request['name'],
            'category' => $request['category'],
            'parking_lot_id' => $parkingLot->id,
            'daily_rate' => $request['daily_rate'],
            'night_rate' => $request['night_rate'],
            'parking_space' => $request['parking_space'],
        ]);

        return response()->json([
            'success' => 1,
            'massage' => 'Created a new vehicle type',
            'data' => new VehicleTypeResource($vehicleType)
        ], 200);
    }

    /**
     * Display the specified resource.
     * @param  \App\Models\ParkingLot  $parkingLot
     * @param  \App\Models\VehicleType  $vehicleType
     * @return \Illuminate\Http\Response
     */
    public function show(ParkingLot $parkingLot, VehicleType $vehicleType)
    {
        if ($vehicleType->parking_lot_id != $parkingLot->id) {
            abort(404);
        }

        return VehicleTypeResource::collection([$vehicleType]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\VehicleType  $vehicleType
     * @param  \App\Models\ParkingLot  $parkingLot
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ParkingLot $parkingLot, VehicleType $vehicleType)
    {
        if ($vehicleType->parking_lot_id != $parkingLot->id) {
            abort(404);
        }

        //Validate the input data
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string'],
            'category' => ['required', 'string'],
            'daily_rate' => ['required', 'numeric'],
            'night_rate' => ['required', 'numeric'],
            'parking_space' => ['required', 'numeric']
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 1,
                'data' => $validator->errors()
            ], 400);
        }

        // Update vehicle type and save it to the database
        $vehicleType->update(
            $request->only([
                'name',
                'category',
                'daily_rate',
                'night_rate',
                'parking_space'
            ])
        );

        return response()->json([
            'success' => 1,
            'massage' => 'Vehicle type updated',
            'data' => new VehicleTypeResource($vehicleType)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\VehicleType  $vehicleType
     * @param  \App\Models\ParkingLot  $parkingLot
     * @return \Illuminate\Http\Response
     */
    public function destroy(ParkingLot $parkingLot, VehicleType $vehicleType)
    {
        if ($vehicleType->parking_lot_id != $parkingLot->id) {
            abort(404);
        }

        if(!$vehicleType->vehicles->isEmpty()){
            return response()->json([
                'error' => 1,
                'massage' => 'Vehicle type has vehicles and it can\'t be deleted',
            ], 400);
        }

        $vehicleType->delete();

        return response()->json([
            'success' => 1,
            'massage' => 'Vehicle type removed',
        ], 200);
    }
}
