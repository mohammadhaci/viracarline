<?php

namespace App\Enums;

enum VehicleStatus: string
{
    case Purchased = 'purchased';
    case InWorkshop = 'in_workshop';
    case Ready = 'ready';
    case Listed = 'listed';
    case Reserved = 'reserved';
    case Sold = 'sold';
    case Archived = 'archived';
}
