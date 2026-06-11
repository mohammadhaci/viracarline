<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ ($seoTitle ?? null) ? $seoTitle.' — '.$siteCompanyName : $siteCompanyName }}</title>
    @if($seoDescription ?? null)
        <meta name="description" content="{{ $seoDescription }}">
    @endif
    <meta property="og:title" content="{{ $seoTitle ?? $siteCompanyName }}">
    @if($ogImage ?? null)
        <meta property="og:image" content="{{ $ogImage }}">
    @endif

    @php($currentPath = preg_replace('#^/[a-z]{2}(/|$)#', '$1', '/'.request()->path()))
    @foreach(config('locales.supported') as $hreflang)
        <link rel="alternate" hreflang="{{ $hreflang }}" href="{{ url($hreflang.($currentPath === '/' ? '' : '/'.ltrim($currentPath, '/'))) }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ url(config('locales.default').($currentPath === '/' ? '' : '/'.ltrim($currentPath, '/'))) }}">

    @stack('structured-data')

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @if($siteAnalyticsScript)
        {!! $siteAnalyticsScript !!}
    @endif
</head>
<body class="min-h-screen bg-white font-sans text-zinc-900 antialiased">
    <header class="sticky top-0 z-40 border-b border-zinc-200 bg-white/95 backdrop-blur" x-data="{ open: false }">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6">
            <a href="{{ route('home') }}" class="text-lg font-bold tracking-tight">
                {{ $siteCompanyName }}
            </a>

            <nav class="hidden items-center gap-6 md:flex">
                @foreach($headerMenu?->items->whereNull('parent_id') ?? [] as $item)
                    <a href="{{ $item->page ? route('pages.show', ['slug' => $item->page->slug]) : url(app()->getLocale().$item->url) }}"
                       class="text-sm font-medium text-zinc-600 hover:text-zinc-900">
                        {{ $item->label }}
                    </a>
                @endforeach
            </nav>

            <div class="flex items-center gap-4">
                <div class="flex items-center gap-1 text-xs font-semibold uppercase">
                    @foreach(config('locales.supported') as $switchLocale)
                        <a href="{{ url($switchLocale.($currentPath === '/' ? '' : '/'.ltrim($currentPath, '/'))) }}"
                           @class([
                               'rounded px-1.5 py-1',
                               'bg-zinc-900 text-white' => $switchLocale === app()->getLocale(),
                               'text-zinc-500 hover:text-zinc-900' => $switchLocale !== app()->getLocale(),
                           ])>{{ $switchLocale }}</a>
                    @endforeach
                </div>

                <button class="md:hidden" @click="open = !open" aria-label="Menu">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        <nav class="border-t border-zinc-200 px-4 py-3 md:hidden" x-show="open" x-cloak>
            @foreach($headerMenu?->items->whereNull('parent_id') ?? [] as $item)
                <a href="{{ $item->page ? route('pages.show', ['slug' => $item->page->slug]) : url(app()->getLocale().$item->url) }}"
                   class="block py-2 text-sm font-medium text-zinc-700">
                    {{ $item->label }}
                </a>
            @endforeach
        </nav>
    </header>

    <main>
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <footer class="mt-24 border-t border-zinc-200 bg-zinc-950 text-zinc-300">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 md:grid-cols-3">
            <div>
                <p class="text-lg font-bold text-white">{{ $siteCompanyName }}</p>
                @if($siteAddress)
                    <p class="mt-3 whitespace-pre-line text-sm">{{ $siteAddress }}</p>
                @endif
            </div>
            <div class="text-sm">
                @if($sitePhone)<p>{{ $sitePhone }}</p>@endif
                @if($siteEmail)<p class="mt-1">{{ $siteEmail }}</p>@endif
            </div>
            <div class="flex flex-col gap-2 text-sm">
                @foreach($footerMenu?->items->whereNull('parent_id') ?? [] as $item)
                    <a href="{{ $item->page ? route('pages.show', ['slug' => $item->page->slug]) : url(app()->getLocale().$item->url) }}"
                       class="hover:text-white">
                        {{ $item->label }}
                    </a>
                @endforeach
            </div>
        </div>
        <div class="border-t border-zinc-800 py-5 text-center text-xs text-zinc-500">
            © {{ date('Y') }} {{ $siteCompanyName }}
        </div>
    </footer>
</body>
</html>
