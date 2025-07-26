<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NonRenewalNotification extends Notification
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
            ->subject('Regarding Your Seasonal Stay at Kayuta Lake')
            ->greeting('Hi, ' . trim("{$notifiable->f_name} {$notifiable->l_name}") ?: 'Guest' . ',')
            ->line('Thank you for staying with us at Kayuta Lake this past season. We genuinely appreciate your time with us.')
            ->line('After careful consideration, we’ve determined that our campground may not be the best fit for your needs going forward.')
            ->line('As a result, we will not be extending a renewal invitation for the upcoming season.')
            ->line('We understand this may come as unexpected news and we want to ensure transparency in our decision.')
            ->action('View Full Letter', $this->signedUrl)
            ->line('We wish you all the best in your future travels and thank you again for your past patronage.');
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
