<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6">
    <div @class(['grid items-center gap-10 lg:grid-cols-2', 'lg:[&>div]:order-first lg:[&>img]:order-last' => ($data['image_position'] ?? 'left') === 'right'])>
        @if($data['image'] ?? null)
            <img src="{{ asset('storage/'.$data['image']) }}" alt="{{ $data['heading'] ?? '' }}" class="rounded-xl object-cover" loading="lazy">
        @endif
        <div>
            @if($data['heading'] ?? null)
                <h2 class="text-3xl font-bold tracking-tight">{{ $data['heading'] }}</h2>
            @endif
            <div class="prose prose-zinc mt-4 max-w-none">{!! $data['text'] ?? '' !!}</div>
        </div>
    </div>
</section>
