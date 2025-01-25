<?php
namespace App\Mail;

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SurveyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $survey;

    /**
     * Create a new message instance.
     *
     * @param $survey
     */
    public function __construct($survey)
    {
        $this->survey = $survey;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Survey Email')
                    ->view('emails.survey')
                    ->with(['survey' => $this->survey]);
    }
}
