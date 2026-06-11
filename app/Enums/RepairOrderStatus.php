<?php

namespace App\Enums;

enum RepairOrderStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case WaitingParts = 'waiting_parts';
    case Done = 'done';
    case Invoiced = 'invoiced';
}
