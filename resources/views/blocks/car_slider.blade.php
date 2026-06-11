@php
    $vehicles = \App\Models\Vehicle::query()
        ->with('media')
        ->where('is_published', true)
        ->where('status', \App\Enums\VehicleStatus::Listed)
        ->latest()
        ->limit((int) ($data['limit'] ?? 8))
        ->get();
@endphp
<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6">
    @if($data['heading'] ?? null)
        <h2 class="text-3xl font-bold tracking-tight">{{ $data['heading'] }}</h2>
    @endif
    <div class="mt-8 flex snap-x gap-6 overflow-x-auto pb-4">
        @foreach($vehicles as $vehicle)
            <div class="w-80 shrink-0 snap-start">
                @include('partials.vehicle-card', ['vehicle' => $vehicle])
            </div>
        @endforeach
    </div>
</section>
