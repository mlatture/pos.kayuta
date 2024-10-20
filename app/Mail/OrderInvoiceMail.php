<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderInvoiceMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $order; // Variable to hold order data

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Order Placed Notification')
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->view('emails.orderEmail', ['order' => $this->order]);
    }
}
