<section class="mx-auto max-w-3xl px-4 py-16 sm:px-6">
    @if($data['heading'] ?? null)
        <h2 class="text-3xl font-bold tracking-tight">{{ $data['heading'] }}</h2>
    @endif
    @if($data['intro'] ?? null)
        <p class="mt-3 text-zinc-600">{{ $data['intro'] }}</p>
    @endif
    <div class="mt-8">
        @include('partials.lead-form', ['type' => 'contact', 'withPhotos' => $data['with_photos'] ?? false])
    </div>
</section>
