<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionActivated extends Notification
{
    public function __construct(public Subscription $subscription)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $s = $this->subscription;

        return (new MailMessage)
            ->subject('Your SalonSync subscription is active')
            ->greeting("Hello {$notifiable->name},")
            ->line("Thank you for subscribing to the {$s->plan_name} plan for {$s->salon_name}.")
            ->line('Reference no: ' . $s->reference_no)
            ->line('Amount: ₱' . number_format($s->price, 2) . ' per month')
            ->line('Valid until: ' . $s->ends_at->format('M d, Y'))
            ->action('View My Subscription', route('subscriptions.index'));
    }
}