<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Survey;
use App\Mail\SurveyEmail;
use Illuminate\Support\Facades\Mail;

class ProcessSurveyEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $survey;

    /**
     * Create a new job instance.
     *
     * @param Survey $survey
     */
    public function __construct(Survey $survey)
    {
        $this->survey = $survey;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->attempts() > 3) {
            return;
        }

        Mail::to($this->survey->guest_email)->send(new SurveyEmail($this->survey));
        $this->survey->update(['sent' => true]); 

        sleep(5);
    }
}


