<?php

namespace App\Notifications;

use App\Enums\LeadType;
use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewLeadNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Lead $lead) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $type = $this->lead->type === LeadType::VehicleInquiry ? 'Fahrzeuganfrage' : 'Kontaktanfrage';

        $message = (new MailMessage)
            ->subject("Neue {$type}: {$this->lead->name}")
            ->greeting("Neue {$type}")
            ->line("Von: {$this->lead->name} ({$this->lead->email})")
            ->line('Nachricht: '.str($this->lead->message)->limit(300));

        if ($this->lead->vehicle) {
            $message->line("Fahrzeug: {$this->lead->vehicle->brand} {$this->lead->vehicle->model}");
        }

        return $message->action('Lead öffnen', url("/admin/leads/{$this->lead->id}/edit"));
    }
}
