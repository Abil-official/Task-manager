<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\PasswordUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PasswordController extends Controller
{
    /**
     * Show the user's password settings page.
     */
    public function edit(): Response
    {
        return Inertia::render('settings/password');
    }

    /**
     * Update the user's password.
     */
    public function update(PasswordUpdateRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => $request->password,
        ]);

        return back();
    }

    public function store(Request $request)
    {
        // 1. Validation inside controller
        $validated = $request->validate([
            'trip_id' => 'required|exists:trips,id',
            'email' => 'required|email',
        ]);

        // 2. Heavy Business Logic
        $trip = Trip::find($request->trip_id);
        $price = $trip->price;

        // 3. Direct DB interaction
        $booking = new Booking;
        $booking->user_email = $request->email;
        $booking->total = $price;
        $booking->save();

        // 4. Side effects (Slows response)
        Mail::to($request->email)->send(new BookingConfirmed($booking));

        return response()->json($booking);
    }
}
