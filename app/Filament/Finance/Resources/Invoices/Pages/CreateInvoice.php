<?php

namespace App\Filament\Finance\Resources\Invoices\Pages;

use App\Filament\Finance\Resources\Invoices\InvoiceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;
}
