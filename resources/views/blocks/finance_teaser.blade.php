<section class="bg-zinc-50 py-16">
    <div class="mx-auto max-w-3xl px-4 text-center sm:px-6">
        @if($data['heading'] ?? null)
            <h2 class="text-3xl font-bold tracking-tight">{{ $data['heading'] }}</h2>
        @endif
        @if($data['text'] ?? null)
            <p class="mt-4 text-zinc-600">{{ $data['text'] }}</p>
        @endif
        @if($data['cta_label'] ?? null)
            <a href="{{ $data['cta_url'] ?? '#' }}" class="mt-7 inline-block rounded-lg bg-zinc-950 px-7 py-3.5 text-sm font-semibold text-white hover:bg-zinc-800">
                {{ $data['cta_label'] }}
            </a>
        @endif
    </div>
</section>
