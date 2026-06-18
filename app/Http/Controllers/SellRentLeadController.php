<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use App\Models\SellRentLead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SellRentLeadController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'intent' => ['required', 'in:sell,rent'],
            'rent_frequency' => ['nullable', 'in:yearly,monthly'],
            'location' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:residential,commercial'],
            'property_type' => ['nullable', 'string', 'max:40'],
            'bedrooms' => ['nullable', 'string', 'max:20'],
            'area_sqft' => ['nullable', 'string', 'max:40'],
            'furnishing' => ['nullable', 'string', 'max:40'],
            'urgency' => ['nullable', 'string', 'max:40'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:60'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        if ($validated['intent'] === SellRentLead::INTENT_RENT && empty($validated['rent_frequency'])) {
            return response()->json([
                'success' => false,
                'message' => 'Please select rent frequency.',
            ], 422);
        }

        $lead = SellRentLead::create([
            ...$validated,
            'status' => SellRentLead::STATUS_NEW,
        ]);

        $intentLabel = $validated['intent'] === SellRentLead::INTENT_RENT ? 'Rent' : 'Sell';
        AdminNotification::notify(
            'sell_rent_lead',
            'New ' . $intentLabel . ' property lead',
            $validated['name'] . ' — ' . $validated['location'],
            null
        );

        return response()->json([
            'success' => (bool) $lead,
            'message' => $validated['intent'] === SellRentLead::INTENT_RENT
                ? 'Thank you! Your rent listing request has been received. Our team will contact you shortly.'
                : 'Thank you! Your sell listing request has been received. Our team will contact you shortly.',
        ]);
    }
}
