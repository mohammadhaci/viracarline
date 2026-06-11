<?php

namespace App\Filament\Finance\Resources\Expenses\Pages;

use App\Filament\Finance\Resources\Expenses\ExpenseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExpense extends CreateRecord
{
    protected static string $resource = ExpenseResource::class;
}
