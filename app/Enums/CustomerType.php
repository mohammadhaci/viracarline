<?php

namespace App\Enums;

enum CustomerType: string
{
    case Buyer = 'buyer';
    case RepairClient = 'repair_client';
    case Seller = 'seller';
}
