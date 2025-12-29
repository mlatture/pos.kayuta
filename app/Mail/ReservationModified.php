<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationModified extends Mailable
{
    use Queueable, SerializesModels;

    public $oldCartId;
    public $newCartId;
    public $creditAmount;
    public $newReservations;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($oldCartId, $newCartId, $creditAmount, $newReservations)
    {
        $this->oldCartId = $oldCartId;
        $this->newCartId = $newCartId;
        $this->creditAmount = $creditAmount;
        $this->newReservations = $newReservations;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Reservation Modified - #' . $this->newCartId,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.reservations.modified',
            with: [
                'oldCartId' => $this->oldCartId,
                'newCartId' => $this->newCartId,
                'creditAmount' => $this->creditAmount,
                'newReservations' => $this->newReservations,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
