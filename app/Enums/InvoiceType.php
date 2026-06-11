<?php

namespace App\Enums;

enum InvoiceType: string
{
    case VehicleSale = 'vehicle_sale';
    case Repair = 'repair';
}
