<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6">
    @if($data['heading'] ?? null)
        <h2 class="text-3xl font-bold tracking-tight">{{ $data['heading'] }}</h2>
    @endif
    <div class="mt-8 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
        @foreach($data['members'] ?? [] as $member)
            <div class="text-center">
                @if($member['photo'] ?? null)
                    <img src="{{ asset('storage/'.$member['photo']) }}" alt="{{ $member['name'] ?? '' }}" class="mx-auto h-32 w-32 rounded-full object-cover" loading="lazy">
                @endif
                <p class="mt-4 font-semibold">{{ $member['name'] ?? '' }}</p>
                @if($member['role'] ?? null)
                    <p class="text-sm text-zinc-600">{{ $member['role'] }}</p>
                @endif
            </div>
        @endforeach
    </div>
</section>
