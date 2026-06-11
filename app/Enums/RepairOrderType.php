<?php

namespace App\Enums;

enum RepairOrderType: string
{
    case Internal = 'internal';
    case Customer = 'customer';
}
