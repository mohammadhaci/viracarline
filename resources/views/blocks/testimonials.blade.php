<section class="bg-zinc-50 py-16">
    <div class="mx-auto grid max-w-7xl gap-8 px-4 sm:px-6 md:grid-cols-2 lg:grid-cols-3">
        @foreach($data['testimonials'] ?? [] as $testimonial)
            <figure class="rounded-xl bg-white p-6 shadow-sm">
                <blockquote class="text-sm text-zinc-700">«{{ $testimonial['quote'] ?? '' }}»</blockquote>
                <figcaption class="mt-4 text-sm font-semibold">{{ $testimonial['name'] ?? '' }}</figcaption>
            </figure>
        @endforeach
    </div>
</section>
