<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6">
    <div class="flex flex-wrap items-center justify-center gap-10 opacity-70">
        @foreach($data['logos'] ?? [] as $logo)
            <img src="{{ asset('storage/'.$logo) }}" alt="" class="h-10 object-contain grayscale" loading="lazy">
        @endforeach
    </div>
</section>
