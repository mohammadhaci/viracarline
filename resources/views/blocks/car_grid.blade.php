@php
    $vehicles = \App\Models\Vehicle::query()
        ->where('is_published', true)
        ->where('status', \App\Enums\VehicleStatus::Listed)
        ->when($data['featured_only'] ?? false, fn ($q) => $q->where('is_featured', true))
        ->latest()
        ->limit((int) ($data['limit'] ?? 6))
        ->get();
@endphp
<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6">
    @if($data['heading'] ?? null)
        <h2 class="text-3xl font-bold tracking-tight">{{ $data['heading'] }}</h2>
    @endif
    <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @each('partials.vehicle-card', $vehicles, 'vehicle')
    </div>
</section>
