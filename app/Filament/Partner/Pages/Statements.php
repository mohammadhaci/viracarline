<?php

namespace App\Filament\Partner\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\URL;

class Statements extends Page
{
    protected string $view = 'filament.partner.pages.statements';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $title = 'Dokumente';

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $partner = auth()->user()->partner;

        $documents = $partner
            ? $partner->getMedia('statements')->map(fn ($media) => [
                'name' => $media->name,
                'file_name' => $media->file_name,
                'size' => $media->humanReadableSize,
                'date' => $media->created_at,
                // Signed URL, 30 minutes — private disk stays unexposed (plan §6).
                'url' => URL::temporarySignedRoute(
                    'partner.statements.download',
                    now()->addMinutes(30),
                    ['media' => $media->id],
                ),
            ])
            : collect();

        return ['documents' => $documents];
    }
}
