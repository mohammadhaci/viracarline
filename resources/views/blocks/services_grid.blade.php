<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6">
    @if($data['heading'] ?? null)
        <h2 class="text-3xl font-bold tracking-tight">{{ $data['heading'] }}</h2>
    @endif
    <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($data['services'] ?? [] as $service)
            <div class="rounded-xl border border-zinc-200 p-6">
                <h3 class="font-semibold">{{ $service['title'] ?? '' }}</h3>
                @if($service['text'] ?? null)
                    <p class="mt-2 text-sm text-zinc-600">{{ $service['text'] }}</p>
                @endif
            </div>
        @endforeach
    </div>
</section>
