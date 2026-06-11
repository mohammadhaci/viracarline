<x-filament-widgets::widget>
    <section class="rounded-2xl bg-gray-950 px-8 py-10 text-center text-white shadow-lg dark:bg-gray-900">
        @if($amount)
            <p class="text-sm font-medium uppercase tracking-wide text-gray-400">Ihr aktueller Stand</p>
            <p class="mt-3 text-5xl font-extrabold tracking-tight sm:text-6xl">{{ $amount }}</p>
            @if($note)
                <p class="mt-4 text-sm text-gray-300">{{ $note }}</p>
            @endif
        @else
            <p class="text-sm text-gray-300">Ihrem Konto ist noch kein Partnerprofil zugeordnet. Bitte kontaktieren Sie uns.</p>
        @endif
    </section>
</x-filament-widgets::widget>
