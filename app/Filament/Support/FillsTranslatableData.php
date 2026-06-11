<?php

namespace App\Filament\Support;

/**
 * For EditRecord pages of translatable models: replaces each translatable
 * attribute (which resolves to the current locale's string) with the full
 * per-locale translations array so the locale tabs are populated.
 */
trait FillsTranslatableData
{
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $model = static::getResource()::getModel();

        foreach ((new $model)->getTranslatableAttributes() as $attribute) {
            $data[$attribute] = $this->getRecord()->getTranslations($attribute);
        }

        return $data;
    }
}
