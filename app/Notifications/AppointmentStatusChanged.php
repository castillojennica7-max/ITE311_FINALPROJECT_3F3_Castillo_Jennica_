<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentStatusChanged extends Notification
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

        $message = match ($a->status) {
            'confirmed' => 'Good news! Your appointment has been confirmed. We look forward to seeing you.',
            'completed' => 'Thank you for visiting SalonSync. We hope to see you again soon!',
            'cancelled' => 'Your appointment has been cancelled. You can book a new one anytime.',
            default     => 'Your appointment status has been updated.',
        };

        return (new MailMessage)
            ->subject('SalonSync appointment update: ' . ucfirst($a->status))
            ->greeting("Hello {$notifiable->name},")
            ->line($message)
            ->line("Service: {$a->service_name}")
            ->line('Date and time: ' . $a->appointment_at->format('M d, Y h:i A'))
            ->line('Status: ' . ucfirst($a->status))
            ->action('View My Appointments', route('appointments.index'));
    }
}