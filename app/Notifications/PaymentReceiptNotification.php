<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceiptNotification extends Notification
{
    use Queueable;

    public $amount, $method, $plan, $date, $signedUrl;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($amount, $method, $plan, $date, $signedUrl)
    {
        $this->amount = $amount;
        $this->method = $method;
        $this->plan = $plan;
        $this->date = $date;
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
                ->subject('Payment Receipt Notification')
                ->greeting('Hi, ' . trim("{$notifiable->f_name} {$notifiable->l_name}") ?: 'Guest' . ',')
                ->line('Thank you for your recent payment!')
                ->line('Here are your payment details:')
                ->line('🧾 Amount: $' . number_format($this->amount, 2))
                ->line('💳 Method: ' . ucfirst($this->method))
                ->line('📅 Date: ' . $this->date->format('F d, Y'))
                ->line('📘 Plan: ' . ucfirst(str_replace('_', ' ', $this->plan)))
                ->line('This email serves as your official receipt.')
                ->line('If you have any questions regarding this payment, feel free to reach out.')
                ->line('Thank you for your continued support!')
                ->action('View Full Receipt', $this->signedUrl);
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
