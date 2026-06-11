<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6">
    <div class="grid grid-cols-2 gap-4 md:grid-cols-3">
        @foreach($data['images'] ?? [] as $image)
            <img src="{{ asset('storage/'.$image) }}" alt="" class="aspect-[4/3] w-full rounded-lg object-cover" loading="lazy">
        @endforeach
    </div>
</section>
