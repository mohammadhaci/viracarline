@extends('layouts.public')

@php($seoTitle = $vehicle->title ?: "{$vehicle->brand} {$vehicle->model}")
@php($seoDescription = str(strip_tags((string) $vehicle->description))->limit(160)->toString() ?: null)

@push('structured-data')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Car',
            'name' => $seoTitle,
            'brand' => ['@type' => 'Brand', 'name' => $vehicle->brand],
            'model' => $vehicle->model,
            'vehicleModelDate' => (string) $vehicle->year,
            'mileageFromOdometer' => ['@type' => 'QuantitativeValue', 'value' => $vehicle->mileage_km, 'unitCode' => 'KMT'],
            'fuelType' => $vehicle->fuel,
            'offers' => $vehicle->show_price && $vehicle->asking_price !== null ? [
                '@type' => 'Offer',
                'price' => $vehicle->asking_price,
                'priceCurrency' => 'CHF',
                'availability' => 'https://schema.org/InStock',
            ] : null,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endpush

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6">
        <div class="grid gap-10 lg:grid-cols-5">
            <div class="lg:col-span-3" x-data="{ active: 0 }">
                @php($photos = $vehicle->getMedia('photos'))
                <div class="aspect-[4/3] overflow-hidden rounded-xl bg-zinc-100">
                    @forelse($photos as $index => $photo)
                        <img src="{{ $photo->getUrl() }}" alt="{{ $seoTitle }}" x-show="active === {{ $index }}" class="h-full w-full object-cover" @if($index > 0) x-cloak loading="lazy" @endif>
                    @empty
                        <div class="flex h-full items-center justify-center text-zinc-400">
                            <svg class="h-20 w-20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m12 0a1.5 1.5 0 01-3 0m3 0h1.5a1.5 1.5 0 001.5-1.5v-2.36a3 3 0 00-.62-1.83l-1.55-2a3 3 0 00-2.37-1.16H7.34a3 3 0 00-2.6 1.51L3.4 13.4a3 3 0 00-.4 1.5v2.35a1.5 1.5 0 001.5 1.5h1.35"/>
                            </svg>
                        </div>
                    @endforelse
                </div>
                @if($photos->count() > 1)
                    <div class="mt-3 flex gap-2 overflow-x-auto">
                        @foreach($photos as $index => $photo)
                            <button @click="active = {{ $index }}" class="h-20 w-28 shrink-0 overflow-hidden rounded-lg border-2" :class="active === {{ $index }} ? 'border-zinc-900' : 'border-transparent'">
                                <img src="{{ $photo->getUrl() }}" alt="" class="h-full w-full object-cover" loading="lazy">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="lg:col-span-2">
                <h1 class="text-3xl font-extrabold tracking-tight">{{ $seoTitle }}</h1>
                <p class="mt-4 text-3xl font-bold">
                    {{ $vehicle->show_price && $vehicle->asking_price !== null ? \App\Support\SwissMoney::format($vehicle->asking_price) : __('Price on request') }}
                </p>

                <dl class="mt-8 grid grid-cols-2 gap-x-6 gap-y-3 rounded-xl border border-zinc-200 p-5 text-sm">
                    <dt class="font-medium text-zinc-500">{{ __('Brand') }}</dt><dd>{{ $vehicle->brand }} {{ $vehicle->model }}</dd>
                    <dt class="font-medium text-zinc-500">{{ __('Year') }}</dt><dd>{{ $vehicle->year }}</dd>
                    <dt class="font-medium text-zinc-500">{{ __('Mileage') }}</dt><dd>{{ number_format($vehicle->mileage_km, 0, '.', "'") }} km</dd>
                    <dt class="font-medium text-zinc-500">{{ __('Fuel') }}</dt><dd>{{ __($vehicle->fuel) }}</dd>
                    <dt class="font-medium text-zinc-500">{{ __('Transmission') }}</dt><dd>{{ __($vehicle->transmission) }}</dd>
                    @if($vehicle->color)
                        <dt class="font-medium text-zinc-500">{{ __('Color') }}</dt><dd>{{ $vehicle->color }}</dd>
                    @endif
                </dl>

                @if($vehicle->description)
                    <div class="prose prose-zinc mt-8 max-w-none text-sm">{!! $vehicle->description !!}</div>
                @endif

                <div class="mt-10 rounded-xl border border-zinc-200 p-5">
                    <h2 class="text-lg font-semibold">{{ __('Send inquiry') }}</h2>
                    <div class="mt-4">
                        @include('partials.lead-form', ['type' => 'vehicle_inquiry', 'vehicle' => $vehicle])
                    </div>
                </div>
            </div>
        </div>

        @if($similar->isNotEmpty())
            <h2 class="mt-20 text-2xl font-bold tracking-tight">{{ __('Similar vehicles') }}</h2>
            <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @each('partials.vehicle-card', $similar, 'vehicle')
            </div>
        @endif
    </section>
@endsection
