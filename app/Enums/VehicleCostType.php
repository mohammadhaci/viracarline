<?php

namespace App\Enums;

enum VehicleCostType: string
{
    case Repair = 'repair';
    case Transport = 'transport';
    case Fees = 'fees';
    case Other = 'other';
}
