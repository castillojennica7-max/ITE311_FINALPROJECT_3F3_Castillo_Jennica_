<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentBooked extends Notification
{
    public function __construct(public Appointment $appointment)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $a = $this->appointment;

        return (new MailMessage)
            ->subject('Your SalonSync appointment request')
            ->greeting("Hello {$notifiable->name},")
            ->line('Thank you for booking with SalonSync. Here are your appointment details:')
            ->line("Service: {$a->service_name}")
            ->line('Date and time: ' . $a->appointment_at->format('M d, Y h:i A'))
            ->line('Stylist: ' . ($a->staff?->name ?? 'Any available'))
            ->line('Price: ₱' . number_format($a->service_price, 2))
            ->line('Status: ' . ucfirst($a->status))
            ->action('View My Appointments', route('appointments.index'))
            ->line('We will let you know once your appointment is confirmed.');
    }
}