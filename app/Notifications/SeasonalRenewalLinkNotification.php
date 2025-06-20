<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SeasonalRenewalLinkNotification extends Notification
{
    use Queueable;

    protected $signedUrl;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($signedUrl)
    {
        //
        $this->signedUrl = $signedUrl;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->subject('Your Seasonal Renewal Invitation')
            ->greeting('Hi, ' . trim("{$notifiable->f_name} {$notifiable->l_name}") ?: 'Guest' . ',')
            ->line('We’re now accepting seasonal renewals for the upcoming year.')
            ->action('Renew Your Site', $this->signedUrl)
            ->line('This secure link will expire in 14 days.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
                //
            ];
    }
}
