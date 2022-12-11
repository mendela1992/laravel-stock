<?php

namespace Mendela92\Stock\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use function url;

class LowStockLevelNotification extends Notification
{
    use Queueable;

    private $model;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Low stock level for " . $this->model->title)
            ->greeting("Hi there,")
            ->line("Receiving this email because SendMe detected a low in stock for " . $this->model->title .
                ". The current stock is " . $this->model->stock . ".")
            ->line("Please consider re-ordering.")
            ->action('View ' . $this->model->title, url('/'));
    }
}
