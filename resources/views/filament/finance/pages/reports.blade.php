<x-filament-panels::page>
    <div class="flex flex-wrap items-end gap-4">
        <label class="grid gap-1 text-sm font-medium">
            Jahr
            <select wire:model.live="year" class="fi-select-input rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900">
                @foreach($years as $availableYear)
                    <option value="{{ $availableYear }}">{{ $availableYear }}</option>
                @endforeach
            </select>
        </label>
        <label class="grid gap-1 text-sm font-medium">
            Quartal
            <select wire:model.live="quarter" class="fi-select-input rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900">
                @foreach([1, 2, 3, 4] as $availableQuarter)
                    <option value="{{ $availableQuarter }}">Q{{ $availableQuarter }}</option>
                @endforeach
            </select>
        </label>
        <x-filament::button wire:click="exportCsv" icon="heroicon-o-arrow-down-tray">
            CSV exportieren
        </x-filament::button>
    </div>

    <div class="mt-6 grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">
            <p class="text-sm text-gray-500">Umsatz (netto)</p>
            <p class="mt-1 text-2xl font-bold">{{ \App\Support\SwissMoney::format($summary['revenue']) }}</p>
            <p class="text-xs text-gray-500">{{ $summary['invoice_count'] }} Rechnungen</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">
            <p class="text-sm text-gray-500">Ausgaben</p>
            <p class="mt-1 text-2xl font-bold">{{ \App\Support\SwissMoney::format($summary['expenses']) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">
            <p class="text-sm text-gray-500">Offene Posten (Debitoren)</p>
            <p class="mt-1 text-2xl font-bold">{{ \App\Support\SwissMoney::format($summary['open_amount']) }}</p>
        </div>
    </div>

    <div class="mt-6 rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">
        <h2 class="text-lg font-semibold">MWST-Zusammenfassung {{ $vat['quarter'] }}</h2>
        <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-3">
            <div>
                <dt class="text-gray-500">Umsatzsteuer (vereinnahmt)</dt>
                <dd class="font-semibold">{{ \App\Support\SwissMoney::format($vat['vat_collected']) }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Vorsteuer (bezahlt)</dt>
                <dd class="font-semibold">{{ \App\Support\SwissMoney::format($vat['vat_paid']) }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Geschuldete MWST</dt>
                <dd class="font-semibold">{{ \App\Support\SwissMoney::format($vat['vat_due']) }}</dd>
            </div>
        </dl>
    </div>
</x-filament-panels::page>
