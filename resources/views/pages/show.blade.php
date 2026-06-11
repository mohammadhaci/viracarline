@extends('layouts.public')

@section('content')
    @forelse($blocks as $block)
        @includeIf('blocks.'.($block['type'] ?? ''), ['data' => $block['data'] ?? []])
    @empty
        <section class="mx-auto max-w-3xl px-4 py-20">
            <h1 class="text-3xl font-bold">{{ $page->title }}</h1>
        </section>
    @endforelse
@endsection
