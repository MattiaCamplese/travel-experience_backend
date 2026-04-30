<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordRecoveryNotification extends Notification
{
    public function __construct(private string $resetLink) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Recupero Password - Travel Experience')
            ->greeting('Ciao ' . $notifiable->first_name . '!')
            ->line('Hai richiesto il recupero della password. Clicca sul bottone qui sotto per reimpostarla.')
            ->action('Reimposta Password', $this->resetLink)
            ->line('Il link scade tra **10 minuti**.')
            ->line('Se non hai richiesto il recupero, ignora questa email.');
    }
}
