<?php

namespace App\Http\Controllers;

use App\Enums\VehicleStatus;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function index(Request $request): View
    {
        $query = Vehicle::query()
            ->where('is_published', true)
            ->whereIn('status', [VehicleStatus::Listed, VehicleStatus::Reserved]);

        $vehicles = $query
            ->with('media')
            ->when($request->filled('brand'), fn ($q) => $q->where('brand', $request->string('brand')))
            ->when($request->filled('fuel'), fn ($q) => $q->where('fuel', $request->string('fuel')))
            ->when($request->filled('transmission'), fn ($q) => $q->where('transmission', $request->string('transmission')))
            ->when($request->filled('price_max'), fn ($q) => $q->where('asking_price', '<=', (float) $request->input('price_max')))
            ->when($request->filled('year_min'), fn ($q) => $q->where('year', '>=', (int) $request->input('year_min')))
            ->when($request->filled('mileage_max'), fn ($q) => $q->where('mileage_km', '<=', (int) $request->input('mileage_max')))
            ->orderByDesc('is_featured')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $brands = Vehicle::query()
            ->where('is_published', true)
            ->distinct()
            ->orderBy('brand')
            ->pluck('brand');

        return view('vehicles.index', compact('vehicles', 'brands'));
    }

    public function show(string $locale, string $slug): View
    {
        $vehicle = Vehicle::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $similar = Vehicle::query()
            ->where('is_published', true)
            ->where('status', VehicleStatus::Listed)
            ->whereKeyNot($vehicle->id)
            ->where('brand', $vehicle->brand)
            ->limit(3)
            ->get();

        if ($similar->count() < 3) {
            $similar = $similar->merge(
                Vehicle::query()
                    ->where('is_published', true)
                    ->where('status', VehicleStatus::Listed)
                    ->whereKeyNot($vehicle->id)
                    ->whereNotIn('id', $similar->pluck('id'))
                    ->latest()
                    ->limit(3 - $similar->count())
                    ->get(),
            );
        }

        return view('vehicles.show', compact('vehicle', 'similar'));
    }
}
