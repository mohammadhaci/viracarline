@extends('layouts.public')

@php($seoTitle = __('Vehicles'))

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6">
        <h1 class="text-4xl font-extrabold tracking-tight">{{ __('Vehicles') }}</h1>

        <form method="GET" class="mt-8 grid gap-3 rounded-xl border border-zinc-200 bg-zinc-50 p-4 sm:grid-cols-3 lg:grid-cols-6">
            <select name="brand" class="rounded-lg border-zinc-300 text-sm">
                <option value="">{{ __('All brands') }}</option>
                @foreach($brands as $brand)
                    <option value="{{ $brand }}" @selected(request('brand') === $brand)>{{ $brand }}</option>
                @endforeach
            </select>
            <select name="fuel" class="rounded-lg border-zinc-300 text-sm">
                <option value="">{{ __('Fuel') }}</option>
                @foreach(['petrol', 'diesel', 'hybrid', 'electric'] as $fuel)
                    <option value="{{ $fuel }}" @selected(request('fuel') === $fuel)>{{ __($fuel) }}</option>
                @endforeach
            </select>
            <select name="transmission" class="rounded-lg border-zinc-300 text-sm">
                <option value="">{{ __('Transmission') }}</option>
                @foreach(['manual', 'automatic'] as $transmission)
                    <option value="{{ $transmission }}" @selected(request('transmission') === $transmission)>{{ __($transmission) }}</option>
                @endforeach
            </select>
            <input type="number" name="price_max" value="{{ request('price_max') }}" placeholder="{{ __('Max. price') }}" min="0" class="rounded-lg border-zinc-300 text-sm">
            <input type="number" name="year_min" value="{{ request('year_min') }}" placeholder="{{ __('Min. year') }}" min="1990" class="rounded-lg border-zinc-300 text-sm">
            <div class="flex gap-2">
                <input type="number" name="mileage_max" value="{{ request('mileage_max') }}" placeholder="{{ __('Max. mileage') }}" min="0" class="w-full rounded-lg border-zinc-300 text-sm">
                <button type="submit" class="shrink-0 rounded-lg bg-zinc-950 px-4 text-sm font-semibold text-white hover:bg-zinc-800">{{ __('Apply') }}</button>
            </div>
        </form>

        @if($vehicles->isEmpty())
            <p class="mt-16 text-center text-zinc-500">{{ __('No vehicles found.') }}</p>
        @else
            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @each('partials.vehicle-card', $vehicles, 'vehicle')
            </div>
            <div class="mt-10">
                {{ $vehicles->links() }}
            </div>
        @endif
    </section>
@endsection
