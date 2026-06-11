@php
    $url = $data['url'] ?? '';
    $embed = preg_match('#(?:youtube\.com/watch\?v=|youtu\.be/)([\w-]+)#', $url, $m)
        ? 'https://www.youtube-nocookie.com/embed/'.$m[1]
        : (preg_match('#vimeo\.com/(\d+)#', $url, $m) ? 'https://player.vimeo.com/video/'.$m[1] : null);
@endphp
@if($embed)
    <section class="mx-auto max-w-4xl px-4 py-16 sm:px-6">
        <iframe src="{{ $embed }}" class="aspect-video w-full rounded-xl border-0" loading="lazy" allowfullscreen title="Video"></iframe>
    </section>
@endif
