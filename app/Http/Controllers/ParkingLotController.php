<?php

namespace App\Http\Controllers;

use App\Http\Resources\ParkingLotResource;
use Illuminate\Http\Request;
use App\Models\ParkingLot;
use Illuminate\Support\Facades\Validator;

class ParkingLotController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'success' => 1,
            'data' => ParkingLotResource::collection(ParkingLot::all())
        ], 200);
    }

    /**
     * Display a parking lot capasaty.
     *
     * @param \App\Models\ParkingLot $parkingLot
     * @return \Illuminate\Http\Response
     */
    public function parkingLotSpace(ParkingLot $parkingLot)
    {
        return response()->json([
            'success' => '1',
            'data' => [
                'id' => $parkingLot->id,
                'space' => $parkingLot->space,
                'free_spaces' => $parkingLot->remainingLotSpaces(),
                'vehicles' => $parkingLot->vehicles->filter(function ($vehicle) {
                    return $vehicle->in_parking == true;
                })->count()
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validate the input data
        $validator = Validator::make($request->all(), [
            'address' => ['required', 'string'],
            'space' =>  ['required', 'numeric', 'between:1,1000000'],
            'timezone' => ['required', 'string'],
            'currency' => ['required', 'string']
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 1,
                'data' => $validator->errors()
            ], 400);
        }

        //Create a new parking lot and save it to the database
        $parkingLot = ParkingLot::create([
            'address' => $request['address'],
            'space' => $request['space'],
            'timezone' => $request['timezone'],
            'currency' => $request['currency']
        ]);

        return response()->json([
            'success' => 1,
            'massage' => 'Created a new parking lot',
            'data' => new ParkingLotResource($parkingLot)
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ParkingLot  $parkingLot
     * @return \Illuminate\Http\Response
     */
    public function show(ParkingLot $parkingLot)
    {
        return ParkingLotResource::collection([$parkingLot]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ParkingLot  $parkingLot
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ParkingLot $parkingLot)
    {
        //Validate the input data
        $validator = Validator::make($request->all(), [
            'address' => ['required', 'string'],
            'space' =>  ['required', 'numeric', 'between:1,1000000'],
            'timezone' => ['required', 'string'],
            'currency' => ['required', 'string']
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 1,
                'data' => $validator->errors()
            ], 400);
        }

        // Update parking lot and save it to the database
        $parkingLot->update(
            $request->only([
                'address',
                'space',
                'timezone',
                'currency'
            ])
        );

        return response()->json([
            'success' => 1,
            'massage' => 'Parking lot updated',
            'data' => new ParkingLotResource($parkingLot)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ParkingLot  $parkingLot
     * @return \Illuminate\Http\Response
     */
    public function destroy(ParkingLot $parkingLot)
    {
        $parkingLot->delete();

        return response()->json([
            'success' => 1,
            'massage' => 'Parking lot removed and all vehicles, vehicle types and cards',
        ], 200);
    }
}
