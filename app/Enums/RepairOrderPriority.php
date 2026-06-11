<?php

namespace App\Enums;

enum RepairOrderPriority: string
{
    case Low = 'low';
    case Normal = 'normal';
    case High = 'high';
}
