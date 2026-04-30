<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OtpEmailVerification extends Notification
{
    public function __construct(private string $code) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Verifica la tua email - Travel Experience')
            ->greeting('Ciao ' . $notifiable->first_name . '!')
            ->line('Il tuo codice di verifica è:')
            ->line('## ' . $this->code)
            ->line('Il codice scade tra **15 minuti**.')
            ->line('Se non hai richiesto questa registrazione, ignora questa email.')
            ->salutation('Il team di Travel Experience');
    }
}
