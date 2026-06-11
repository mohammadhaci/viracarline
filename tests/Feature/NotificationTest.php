<?php

use App\Models\Lead;
use App\Models\User;
use App\Models\Vehicle;
use App\Notifications\NewLeadNotification;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
});

it('notifies active admins when a lead is created', function () {
    Notification::fake();

    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole('admin');
    $inactiveAdmin = User::factory()->create(['is_active' => false]);
    $inactiveAdmin->assignRole('admin');
    $mechanic = User::factory()->create(['is_active' => true]);
    $mechanic->assignRole('mechanic');

    $lead = Lead::factory()->create();

    Notification::assertSentTo($admin, NewLeadNotification::class, fn ($notification) => $notification->lead->is($lead));
    Notification::assertNotSentTo($inactiveAdmin, NewLeadNotification::class);
    Notification::assertNotSentTo($mechanic, NewLeadNotification::class);
});

it('notifies admins when a public vehicle inquiry arrives', function () {
    Notification::fake();

    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole('admin');

    $vehicle = Vehicle::factory()->listed()->create();

    $this->post('/de/anfrage', [
        'type' => 'vehicle_inquiry',
        'vehicle_id' => $vehicle->id,
        'name' => 'Hans Muster',
        'email' => 'hans@example.ch',
        'message' => 'Noch verfügbar?',
    ])->assertRedirect();

    Notification::assertSentTo($admin, NewLeadNotification::class);
});

it('renders the notification mail with lead details', function () {
    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole('admin');

    Notification::fake();

    $vehicle = Vehicle::factory()->listed()->create(['brand' => 'BMW', 'model' => '320d']);
    $lead = Lead::factory()->vehicleInquiry()->create(['vehicle_id' => $vehicle->id, 'name' => 'Maria Beispiel']);

    $mail = (new NewLeadNotification($lead))->toMail($admin);

    expect($mail->subject)->toContain('Fahrzeuganfrage')
        ->and($mail->subject)->toContain('Maria Beispiel')
        ->and(implode(' ', array_map(fn ($line) => (string) $line, $mail->introLines)))->toContain('BMW 320d');
});
