<section class="bg-zinc-950 py-16 text-white">
    <div class="mx-auto flex max-w-7xl flex-col items-center gap-6 px-4 text-center sm:px-6">
        <h2 class="text-3xl font-bold tracking-tight">{{ $data['heading'] ?? '' }}</h2>
        @if($data['text'] ?? null)
            <p class="max-w-2xl text-zinc-300">{{ $data['text'] }}</p>
        @endif
        @if($data['cta_label'] ?? null)
            <a href="{{ $data['cta_url'] ?? '#' }}" class="rounded-lg bg-white px-7 py-3.5 text-sm font-semibold text-zinc-950 hover:bg-zinc-200">
                {{ $data['cta_label'] }}
            </a>
        @endif
    </div>
</section>
