<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::orderBy('booking_date', 'desc')
                          ->orderBy('start_time', 'desc')
                          ->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $bookings
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'table_number' => 'required|integer|between:1,3',
            'booking_date' => 'required|date',
            'start_time' => 'required',
            'duration' => 'required|integer|min:1'
        ]);

        $booking = Booking::create([
            'customer_name' => $validated['customer_name'],
            'table_number' => $validated['table_number'],
            'booking_date' => $validated['booking_date'],
            'start_time' => $validated['start_time'],
            'duration' => $validated['duration'],
            'status' => 'active'
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $booking
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'table_number' => 'required|integer|between:1,3',
            'booking_date' => 'required|date',
            'start_time' => 'required',
            'duration' => 'required|integer|min:1'
        ]);

        $booking = Booking::findOrFail($id);
        $booking->update($validated);

        return response()->json([
            'status' => 'success',
            'data' => $booking
        ]);
    }

    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Booking deleted successfully'
        ]);
    }
}