<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceived extends Notification
{
    public function __construct(public Transaction $transaction)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $t = $this->transaction->loadMissing(['salon', 'appointment']);

        return (new MailMessage)
            ->subject('Payment receipt ' . $t->reference_no)
            ->greeting("Hello {$notifiable->name},")
            ->line('We received your payment of ₱' . number_format($t->amount, 2) . " at {$t->salon->name}.")
            ->line('Service: ' . ($t->appointment?->service_name ?? '-'))
            ->line('Payment method: ' . $t->methodLabel())
            ->line('Reference no: ' . $t->reference_no)
            ->action('View receipt', route('transactions.receipt', $t))
            ->line('Thank you!');
    }
}