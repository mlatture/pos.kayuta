<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentDeclinedNotification extends Notification
{
    use Queueable;

    public  $reason, $signedUrl;
    
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($reason, $signedUrl)
    {
        $this->reason = $reason;
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
        return (new MailMessage)
                    ->subject('Payment Declined Notification')
                    ->greeting('Hi, ' . trim("{$notifiable->f_name} {$notifiable->l_name}") ?: 'Guest' . ',')
                    ->line('We regret to inform you that your recent payment attempt was declined.')
                    ->line('Reason: ' . $this->reason)
                    ->line('Please check your payment details and try again.')
                    ->line('If you have any questions or need assistance, please contact our support team.')
                    ->line('Thank you for your attention to this matter.')
                    ->action('View Details', $this->signedUrl);
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
