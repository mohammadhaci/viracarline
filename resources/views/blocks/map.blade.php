@if($data['embed_url'] ?? null)
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6">
        <iframe src="{{ $data['embed_url'] }}" class="h-96 w-full rounded-xl border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Map"></iframe>
    </section>
@endif
