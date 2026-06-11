@extends('layouts.public')

@php($seoTitle = $post->title)
@php($seoDescription = $post->excerpt)

@section('content')
    <article class="mx-auto max-w-3xl px-4 py-12 sm:px-6">
        @if($post->category)
            <p class="text-xs font-semibold uppercase text-zinc-500">{{ $post->category->name }}</p>
        @endif
        <h1 class="mt-2 text-4xl font-extrabold tracking-tight">{{ $post->title }}</h1>
        @if($post->published_at)
            <p class="mt-3 text-sm text-zinc-500">{{ $post->published_at->translatedFormat('d. F Y') }}</p>
        @endif
        @if($image = $post->getFirstMediaUrl('featured_image'))
            <img src="{{ $image }}" alt="{{ $post->title }}" class="mt-8 w-full rounded-xl object-cover">
        @endif
        <div class="prose prose-zinc mt-8 max-w-none">{!! $post->body !!}</div>

        <a href="{{ route('blog.index') }}" class="mt-12 inline-block text-sm font-semibold underline">← {{ __('Back to blog') }}</a>
    </article>
@endsection
