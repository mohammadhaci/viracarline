<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6">
    @if($data['heading'] ?? null)
        <h2 class="text-3xl font-bold tracking-tight">{{ $data['heading'] }}</h2>
    @endif
    <div class="mt-8 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($data['features'] ?? [] as $feature)
            <div>
                <h3 class="font-semibold">{{ $feature['title'] ?? '' }}</h3>
                @if($feature['text'] ?? null)
                    <p class="mt-2 text-sm text-zinc-600">{{ $feature['text'] }}</p>
                @endif
            </div>
        @endforeach
    </div>
</section>
