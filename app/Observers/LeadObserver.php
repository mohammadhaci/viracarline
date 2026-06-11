<?php

namespace App\Observers;

use App\Models\Lead;
use App\Models\User;
use App\Notifications\NewLeadNotification;
use Illuminate\Support\Facades\Notification;

class LeadObserver
{
    public function created(Lead $lead): void
    {
        $admins = User::query()
            ->whereHas('roles', fn ($query) => $query->where('name', 'admin'))
            ->where('is_active', true)
            ->get();

        Notification::send($admins, new NewLeadNotification($lead));
    }
}
