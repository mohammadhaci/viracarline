<?php

namespace App\Http\Controllers;

use App\Enums\LeadType;
use App\Models\Lead;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        // Honeypot: bots fill the hidden "website" field; pretend success.
        if ($request->filled('website')) {
            return back()->with('lead_submitted', true);
        }

        $data = $request->validate([
            'type' => ['required', 'in:contact,vehicle_inquiry'],
            'vehicle_id' => ['nullable', 'integer', 'exists:vehicles,id'],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'message' => ['required', 'string', 'max:5000'],
            'photos' => ['nullable', 'array', 'max:5'],
            'photos.*' => ['file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        if (($data['type'] === LeadType::VehicleInquiry->value) && ! empty($data['vehicle_id'])) {
            abort_unless(
                Vehicle::whereKey($data['vehicle_id'])->where('is_published', true)->exists(),
                422,
            );
        } else {
            $data['vehicle_id'] = null;
        }

        $lead = Lead::create([
            ...collect($data)->except('photos')->all(),
            'locale' => app()->getLocale(),
        ]);

        // Ankauf photos go to the private local disk — never web-accessible (plan §6).
        foreach ($request->file('photos', []) as $photo) {
            $lead->addMedia($photo)->toMediaCollection('photos', 'local');
        }

        return back()->with('lead_submitted', true);
    }
}
