<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Illuminate\Http\Request;
use App\Http\Resources\CardResource;
use App\Models\ParkingLot;
use Illuminate\Support\Facades\Validator;

class CardController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param  \App\Models\ParkingLot $parkingLot
     * @return \Illuminate\Http\Response
     */
    public function index(ParkingLot $parkingLot)
    {
        return response()->json([
            'success' => 1,
            'data' => CardResource::collection($parkingLot->cards()->get())
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     * @param  \App\Models\ParkingLot  $parkingLot
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, ParkingLot $parkingLot)
    {
        //Validate the input data
        $validator = Validator::make($request->all(),[
            'name' => ['required', 'string'],
            'discount' => ['required', 'numeric'],
        ]);
        if($validator->fails()){
            return response()->json([
                'error' => 1,
                'data' => $validator->errors()
            ], 400);
        }

        //Create a new card and save it to the database
        $card = $parkingLot->cards()->create([
            'name' => $request['name'],
            'discount' => $request['discount'],
        ]);

        return response()->json([
            'success' => 1,
            'massage' => 'Created a new card',
            'card' => new CardResource($card)
        ], 200);
    }

    /**
     * Display the specified resource.
     * 
     * @param  \App\Models\ParkingLot $parkingLot
     * @param  \App\Models\Card  $card
     * @return \Illuminate\Http\Response
     */
    public function show(ParkingLot $parkingLot, Card $card)
    {
        if ($card->parking_lot_id != $parkingLot->id) {
            abort(404);
        }

        return CardResource::collection([$card]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Card  $card
     * @param  \App\Models\ParkingLot  $parkingLot
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ParkingLot $parkingLot, Card $card)
    {
        if ($card->parking_lot_id != $parkingLot->id) {
            abort(404);
        }

        //Validate the input data
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string'],
            'discount' => ['required', 'numeric'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 1,
                'data' => $validator->errors()
            ], 400);
        }

        // Update card and save it to the database
        $card->update(
            $request->only([
                'name',
                'discount'
            ])
        );

        return response()->json([
            'success' => 1,
            'massage' => 'Card updated',
            'data' => new CardResource($card)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ParkingLot $parkingLot
     * @param  \App\Models\Card  $card
     * @return \Illuminate\Http\Response
     */
    public function destroy(ParkingLot $parkingLot, Card $card)
    {
        if ($card->parking_lot_id != $parkingLot->id) {
            abort(404);
        }

        if (!$card->vehicles->isEmpty()) {
            return response()->json([
                'error' => 1,
                'massage' => 'Card has vehicles and it can\'t be deleted',
            ], 400);
        }

        $card->delete();

        return response()->json([
            'success' => 1,
            'massage' => 'Card removed',
        ], 200);
    }
}
