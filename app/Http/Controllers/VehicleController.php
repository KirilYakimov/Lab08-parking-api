<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Resources\VehicleResource;
use App\Models\Vehicle;
use App\Models\ParkingLot;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param  \App\Models\ParkingLot  $parkingLot
     * @return \Illuminate\Http\Response
     */
    public function index(ParkingLot $parkingLot)
    {
        return response()->json([
            'success' => 1,
            'data' => VehicleResource::collection($parkingLot->vehicles()->get())
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param  \App\Models\ParkingLot  $parkingLot
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, ParkingLot $parkingLot)
    {
        //Validate the input data
        $validator = Validator::make($request->all(), [
            'brand' => ['required', 'string'],
            'registration_plate' => ['required', 'string'],
            'vehicle_type_id' => ['required', 'numeric'],
            'card_id' => ['sometimes', 'numeric'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 1,
                'data' => $validator->errors()
            ], 400);
        }

        // Check if there is a vehicle with the same registration plate in the parking
        $vehicle_in_parking = $parkingLot->vehicles()->where('in_parking', '=', true)
            ->where('registration_plate', '=', $request['registration_plate'])->first();

        if (!empty($vehicle_in_parking)) {
            return response()->json([
                'error' => 1,
                'massage' => 'Vehicle with same registration plate is registered!'
            ]);
        }

        // Check if the parking spaces are enough to accept the new vehicle
        $v_type = $parkingLot->vehicleTypes->only($request['vehicle_type_id'])[0];
        if ($parkingLot->remainingLotSpaces() < $v_type->parking_space) {
            return response()->json([
                'error' => 1,
                'massage' => 'Parking capacity reached not enough space for the vehicle!'
            ]);
        }

        //Create a new vehicle and save it to the database
        $vehicle = $parkingLot->vehicles()->create([
            'brand' => $request['brand'],
            'registration_plate' => $request['registration_plate'],
            'vehicle_type_id' => $request['vehicle_type_id'],
            'card_id' => $request['card_id'],
            'entered_at' => new DateTime(),
            'in_parking' => true,
        ]);

        return response()->json([
            'success' => 1,
            'massage' => 'Vehicle created succesfully',
            'data' => new VehicleResource($vehicle)
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ParkingLot  $parkingLot
     * @param  \App\Models\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function show(ParkingLot $parkingLot, Vehicle $vehicle)
    {
        if ($vehicle->parking_lot_id != $parkingLot->id) {
            abort(404);
        }

        return VehicleResource::collection([$vehicle]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ParkingLot  $parkingLot
     * @param  \App\Models\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ParkingLot $parkingLot, Vehicle $vehicle)
    {
        if ($vehicle->parking_lot_id != $parkingLot->id) {
            abort(404);
        }

        //Validate the input data
        $validator = Validator::make($request->all(), [
            'brand' => ['required', 'string'],
            'registration_plate' => ['required', 'string'],
            'vehicle_type_id' => ['required', 'numeric'],
            'card_id' => ['sometimes', 'numeric'],
            'entered_at' => ['sometimes', 'date_format:Y-m-d H:i:s'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 1,
                'data' => $validator->errors()
            ], 400);
        }

        // transform datetime to utc 
        if(isset($request['entered_at'])){
            $request['entered_at'] = Helper::adjustTimeZone($request['entered_at'], 'UTC', $parkingLot->timezone)->format('Y-m-d H:i:s');
        }

        $vehicle->update(
            $request->only([
                'brand',
                'registration_plate',
                'vehicle_type_id',
                'card_id',
                'entered_at'
            ])
        );

        return response()->json([
            'success' => 1,
            'massage' => 'Vehicle updated',
            'data' => new VehicleResource($vehicle)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ParkingLot  $parkingLot
     * @param  \App\Models\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function destroy(ParkingLot $parkingLot, Vehicle $vehicle)
    {
        if ($vehicle->parking_lot_id != $parkingLot->id) {
            abort(404);
        }

        $vehicle->delete();

        return response()->json([
            'success' => 1,
            'massage' => 'Vehicle removed',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ParkingLot  $parkingLot
     * @return \Illuminate\Http\Response
     */
    public function fetchCurrentVehicleInfo(Request $request, ParkingLot $parkingLot)
    {
        // if the vehicle is not in the parking return 404
        $vehicle = Vehicle::where('in_parking', '=', true)
            ->where('registration_plate', '=', $request['registration_plate'])
            ->firstOrFail();

        return response()->json([
            'success' => 1,
            'massage' => 'Current vehicle info',
            'data' => array_merge(
                [
                    'id' => $vehicle->id,
                    'registration_plate' => $vehicle->registration_plate
                ],
                $vehicle->calculateParkingPrice()
            )
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ParkingLot  $parkingLot
     * @param  \App\Models\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function exitParking(ParkingLot $parkingLot, Vehicle $vehicle)
    {
        if ($vehicle->parking_lot_id != $parkingLot->id) {
            abort(404);
        }

        // Check if the vechivle is in the parking
        if(!$vehicle->in_parking){
            return response()->json([
                'error' => 1,
                'data' => 'Vehicle is not in the parking!'
            ], 404);
        }

        $time_now = new DateTime('now');
        $vehicle->exited_at = $time_now->format('Y-m-d H:i:s');
        $vehicle->in_parking = false;

        $timezone = $vehicle->parkinglot->timezone;
        $entered_at = Helper::adjustTimeZone($vehicle->entered_at, $timezone);
        $exited_at = Helper::adjustTimeZone($vehicle->exited_at, $timezone);
        
        $data = $vehicle->calculateParkingPrice();
        $vehicle->price_of_exit = $data['total_price'];
        $vehicle->save();

        return response()->json([
            'success' => 1,
            'massage' => 'Vehicle exited the parking',
            'data' => array_merge(
                [
                    'id' => $vehicle->id,
                    'registration_plate' => $vehicle->registration_plate,
                    'entered_at' => $entered_at->format('Y-m-d H:i:s'),
                    'exited_at' => $exited_at->format('Y-m-d H:i:s')
                ],
                $data
            )
        ], 200);
    }
}
