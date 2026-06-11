<x-filament-panels::page>
    @if($documents->isEmpty())
        <p class="text-sm text-gray-500">Es liegen noch keine Dokumente vor.</p>
    @else
        <ul class="divide-y divide-gray-200 rounded-xl border border-gray-200 bg-white dark:divide-gray-700 dark:border-gray-700 dark:bg-gray-900">
            @foreach($documents as $document)
                <li class="flex items-center justify-between gap-4 px-5 py-4">
                    <div>
                        <p class="font-medium">{{ $document['name'] }}</p>
                        <p class="text-xs text-gray-500">{{ $document['date']->format('d.m.Y') }} · {{ $document['size'] }}</p>
                    </div>
                    <a href="{{ $document['url'] }}" class="text-sm font-semibold text-primary-600 hover:underline">
                        Herunterladen
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</x-filament-panels::page>
