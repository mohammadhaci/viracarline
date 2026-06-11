<section class="mx-auto max-w-3xl px-4 py-16 sm:px-6">
    @if($data['heading'] ?? null)
        <h2 class="text-3xl font-bold tracking-tight">{{ $data['heading'] }}</h2>
    @endif
    <div class="mt-8 divide-y divide-zinc-200">
        @foreach($data['items'] ?? [] as $item)
            <details class="group py-4">
                <summary class="cursor-pointer list-none font-medium">{{ $item['question'] ?? '' }}</summary>
                <p class="mt-3 text-sm text-zinc-600">{{ $item['answer'] ?? '' }}</p>
            </details>
        @endforeach
    </div>
</section>
