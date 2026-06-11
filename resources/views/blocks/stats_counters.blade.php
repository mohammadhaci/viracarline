<section class="bg-zinc-50 py-16">
    <div class="mx-auto grid max-w-7xl gap-8 px-4 text-center sm:grid-cols-3 sm:px-6">
        @foreach($data['stats'] ?? [] as $stat)
            <div>
                <p class="text-4xl font-extrabold tracking-tight">{{ $stat['value'] ?? '' }}</p>
                <p class="mt-1 text-sm font-medium text-zinc-600">{{ $stat['label'] ?? '' }}</p>
            </div>
        @endforeach
    </div>
</section>
