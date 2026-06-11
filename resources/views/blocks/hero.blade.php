<section class="relative bg-zinc-950 text-white">
    @if($data['background'] ?? null)
        <img src="{{ asset('storage/'.$data['background']) }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-40" loading="eager">
    @endif
    <div class="relative mx-auto max-w-7xl px-4 py-28 sm:px-6 lg:py-40">
        <h1 class="max-w-3xl text-4xl font-extrabold tracking-tight sm:text-6xl">{{ $data['heading'] ?? '' }}</h1>
        @if($data['subheading'] ?? null)
            <p class="mt-5 max-w-2xl text-lg text-zinc-300">{{ $data['subheading'] }}</p>
        @endif
        @if($data['cta_label'] ?? null)
            <a href="{{ $data['cta_url'] ?? '#' }}" class="mt-9 inline-block rounded-lg bg-white px-7 py-3.5 text-sm font-semibold text-zinc-950 hover:bg-zinc-200">
                {{ $data['cta_label'] }}
            </a>
        @endif
    </div>
</section>
