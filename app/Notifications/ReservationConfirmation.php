<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationConfirmation extends Notification
{
    use Queueable;

    protected $reservation;
    protected $cc = [];
    protected $content;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Reservation $reservation, array $cc = [], $content = null)
    {
        $this->reservation = $reservation;
        $this->cc = $cc;
        $this->content = $content;
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

        $mail = (new MailMessage())
            ->subject('Reservation Confirmation #' . $this->reservation->cartid)

            ->line($this->content ?? '')  

            ->line('Check-in Date: ' . $this->reservation->cid->format('F j, Y'))
            ->line('Check-out Date: ' . $this->reservation->cod->format('F j, Y'))
            ->line('Total Amount: $' . number_format($this->reservation->total, 2))
            ->line('We look forward to your stay with us!')
            ->line('If you have any questions, feel free to contact us at info@kayutalakecampground.com');

        if (!empty($this->cc)) {
            $mail->cc($this->cc);
        }

        return $mail;
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
