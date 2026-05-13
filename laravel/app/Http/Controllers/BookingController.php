<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response as HttpResponse;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $bookings = $request->user()->bookings()->orderBy("starts_at")->get();

        return response()->json($bookings, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'room_name' => 'required|max:100|string',
            'starts_at' => 'required|date|after:now',
            'ends_at' => 'required|date|after:starts_at',
            'note' => 'max:500|string|nullable',
        ]);

        $booking = $request->user()->bookings()->create($data);

        return response()->json($booking, 201);
            
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Booking $booking)
    {
        if ($request->user()->id !== $booking->user_id) {
            return response()->json(["message" => "Встреча вам не принадлежит"], 403);
        }
        return response()->json($booking, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        if ($request->user()->id !== $booking->user_id) {
            return response()->json(["message" => "Встреча вам не принадлежит"], 403);
        }

        $data = $request->validate([
            'room_name' => 'sometimes|stringmax:100|',
            'starts_at' => 'sometimes|date|after:now',
            'ends_at' => 'sometimes|date|after:starts_at',
            'note' => 'string|max:500|nullable',
        ]);
    $booking->update($data);

    return response()->json($booking, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Booking $booking)
    {
        //проверьте что бронь пользователя которыйделает запрос 

            if ($request->user()->id !== $booking->user_id) {
                return response()->json(["message" => "Встреча успешно отменена"], 403);
        }

        $booking->delete();
        return response()->json($booking, 200);


    }
}