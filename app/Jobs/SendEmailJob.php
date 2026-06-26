<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected string $to,
        protected string $subject,
        protected string $body
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::raw($this->body, function($message) {
            $message->to($this->to)
                    ->subject($this->subject);
        });
    }
}
