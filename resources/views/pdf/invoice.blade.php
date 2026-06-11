<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 18px; margin-bottom: 2px; }
        .muted { color: #555; }
        table.lines { width: 100%; border-collapse: collapse; margin-top: 24px; }
        table.lines th, table.lines td { border-bottom: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        table.lines th:nth-child(2), table.lines td:nth-child(2),
        table.lines th:nth-child(3), table.lines td:nth-child(3),
        table.lines th:nth-child(4), table.lines td:nth-child(4) { text-align: right; }
        .totals { margin-top: 16px; width: 40%; margin-left: 60%; }
        .totals td { padding: 3px 8px; }
        .totals .grand { font-weight: bold; border-top: 2px solid #111; }
    </style>
</head>
<body>
    <h1>Rechnung {{ $invoice->number }}</h1>
    <p class="muted">{{ $companyName }}@if($companyAddress) · {{ str_replace("\n", ', ', $companyAddress) }}@endif</p>

    <p>
        <strong>Rechnungsempfänger:</strong> {{ $invoice->customer?->name ?? '—' }}<br>
        <strong>Datum:</strong> {{ $invoice->created_at->format('d.m.Y') }}<br>
        <strong>Fällig bis:</strong> {{ $invoice->due_at?->format('d.m.Y') ?? '—' }}
    </p>

    <table class="lines">
        <thead>
            <tr><th>Beschreibung</th><th>Menge</th><th>Einzelpreis</th><th>Betrag</th></tr>
        </thead>
        <tbody>
            @foreach($invoice->lines as $line)
                <tr>
                    <td>{{ $line->description }}</td>
                    <td>{{ rtrim(rtrim($line->qty, '0'), '.') }}</td>
                    <td>{{ \App\Support\SwissMoney::format($line->unit_price) }}</td>
                    <td>{{ \App\Support\SwissMoney::format((float) $line->qty * (float) $line->unit_price) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td>Zwischensumme</td><td align="right">{{ \App\Support\SwissMoney::format($invoice->subtotal) }}</td></tr>
        <tr><td>MWST {{ rtrim(rtrim($invoice->vat_rate, '0'), '.') }}%</td><td align="right">{{ \App\Support\SwissMoney::format($invoice->vat_amount) }}</td></tr>
        <tr class="grand"><td>Total</td><td align="right">{{ \App\Support\SwissMoney::format($invoice->total) }}</td></tr>
    </table>

    @if((float) $invoice->vat_amount === 0.0)
        <p class="muted">Differenzbesteuerung nach Art. 24a MWSTG — keine MWST ausweisbar.</p>
    @endif
</body>
</html>
