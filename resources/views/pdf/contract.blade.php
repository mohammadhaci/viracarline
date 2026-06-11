<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 18px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        td, th { border: 1px solid #999; padding: 6px 8px; text-align: left; }
        .meta { margin-top: 24px; }
        .signatures { margin-top: 60px; width: 100%; }
        .signatures td { border: none; padding-top: 40px; }
        .line { border-top: 1px solid #111; width: 80%; }
    </style>
</head>
<body>
    <h1>Kaufvertrag — Occasionsfahrzeug</h1>

    <p class="meta">
        <strong>Verkäufer:</strong> {{ $companyName }}@if($companyAddress), {{ str_replace("\n", ', ', $companyAddress) }}@endif<br>
        <strong>Käufer:</strong> {{ $sale->customer->name }}<br>
        <strong>Datum:</strong> {{ $sale->sold_at->format('d.m.Y') }}
    </p>

    <table>
        <tr><th>Fahrzeug</th><td>{{ $sale->vehicle->brand }} {{ $sale->vehicle->model }} @if($sale->vehicle->variant){{ $sale->vehicle->variant }}@endif</td></tr>
        <tr><th>Jahrgang</th><td>{{ $sale->vehicle->year }}</td></tr>
        <tr><th>VIN</th><td>{{ $sale->vehicle->vin ?? '—' }}</td></tr>
        <tr><th>Kilometerstand</th><td>{{ number_format($sale->vehicle->mileage_km, 0, '.', "'") }} km</td></tr>
        <tr><th>Kaufpreis</th><td><strong>{{ \App\Support\SwissMoney::format($sale->price) }}</strong></td></tr>
        <tr><th>MWST</th><td>{{ $sale->vat_mode === 'margin' ? 'Differenzbesteuert (keine MWST ausweisbar)' : 'Inkl. MWST' }}</td></tr>
    </table>

    <p class="meta">
        Das Fahrzeug wird wie besichtigt und probegefahren verkauft. Gewährleistung gemäss
        gesetzlichen Bestimmungen bzw. separater Vereinbarung.
    </p>

    <table class="signatures">
        <tr>
            <td><div class="line"></div>Verkäufer: {{ $companyName }}</td>
            <td><div class="line"></div>Käufer: {{ $sale->customer->name }}</td>
        </tr>
    </table>
</body>
</html>
