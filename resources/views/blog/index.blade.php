@extends('layouts.public')

@php($seoTitle = __('Blog'))

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6">
        <h1 class="text-4xl font-extrabold tracking-tight">{{ __('Blog') }}</h1>

        <div class="mt-10 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($posts as $post)
                <article class="overflow-hidden rounded-xl border border-zinc-200">
                    @if($image = $post->getFirstMediaUrl('featured_image'))
                        <img src="{{ $image }}" alt="{{ $post->title }}" class="aspect-[16/9] w-full object-cover" loading="lazy">
                    @endif
                    <div class="p-5">
                        @if($post->category)
                            <p class="text-xs font-semibold uppercase text-zinc-500">{{ $post->category->name }}</p>
                        @endif
                        <h2 class="mt-1 text-lg font-semibold">
                            <a href="{{ route('blog.show', ['slug' => $post->slug]) }}" class="hover:underline">{{ $post->title }}</a>
                        </h2>
                        @if($post->excerpt)
                            <p class="mt-2 text-sm text-zinc-600">{{ $post->excerpt }}</p>
                        @endif
                        <a href="{{ route('blog.show', ['slug' => $post->slug]) }}" class="mt-4 inline-block text-sm font-semibold underline">
                            {{ __('Read more') }}
                        </a>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-10">
            {{ $posts->links() }}
        </div>
    </section>
@endsection
