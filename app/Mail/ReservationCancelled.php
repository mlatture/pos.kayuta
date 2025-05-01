<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationCancelled extends Mailable
{
    use Queueable, SerializesModels;

    public $cartid;
    public $sites;
    public $refundMethod;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($cartid, $sites, $refundMethod)
    {
        //

        $this->cartid = $cartid;
        $this->sites = $sites;
        $this->refundMethod = $refundMethod;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Reservation Cancelled - #' . $this->cartid,
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
            view: 'emails.reservation_cancelled',
            with: [
                'cartid' => $this->cartid,
                'sites' => $this->sites,
                'refundMethod' => ucfirst(str_replace('-', ' ', $this->refundMethod))
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
