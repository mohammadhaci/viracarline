<a href="{{ route('vehicles.show', ['slug' => $vehicle->slug]) }}"
   class="group overflow-hidden rounded-xl border border-zinc-200 bg-white transition hover:shadow-lg">
    <div class="relative aspect-[4/3] bg-zinc-100">
        @if($photo = $vehicle->getFirstMediaUrl('photos'))
            <img src="{{ $photo }}" alt="{{ $vehicle->title }}" class="h-full w-full object-cover transition group-hover:scale-105" loading="lazy">
        @else
            <div class="flex h-full items-center justify-center text-zinc-400">
                <svg class="h-14 w-14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m12 0a1.5 1.5 0 01-3 0m3 0h1.5a1.5 1.5 0 001.5-1.5v-2.36a3 3 0 00-.62-1.83l-1.55-2a3 3 0 00-2.37-1.16H7.34a3 3 0 00-2.6 1.51L3.4 13.4a3 3 0 00-.4 1.5v2.35a1.5 1.5 0 001.5 1.5h1.35"/>
                </svg>
            </div>
        @endif
        @if($vehicle->status === \App\Enums\VehicleStatus::Reserved)
            <span class="absolute left-3 top-3 rounded bg-amber-500 px-2 py-1 text-xs font-semibold text-white">{{ __('Reserved') }}</span>
        @endif
    </div>
    <div class="p-4">
        <h3 class="font-semibold">{{ $vehicle->title ?: "{$vehicle->brand} {$vehicle->model}" }}</h3>
        <p class="mt-1 text-sm text-zinc-500">
            {{ $vehicle->year }} · {{ number_format($vehicle->mileage_km, 0, '.', "'") }} km · {{ __($vehicle->fuel) }}
        </p>
        <p class="mt-3 text-lg font-bold">
            {{ $vehicle->show_price && $vehicle->asking_price !== null ? \App\Support\SwissMoney::format($vehicle->asking_price) : __('Price on request') }}
        </p>
    </div>
</a>
