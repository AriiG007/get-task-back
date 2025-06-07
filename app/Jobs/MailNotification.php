<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MailNotification implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected string $email;
    protected string $subject;
    protected string $message;

    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(string $email, string $subject, string $message)
    {
        $this->email = $email;
        $this->subject = $subject;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        Log::info("Job Sending email to {$this->email} with subject: {$this->subject}");

        Mail::raw($this->message, function ($mail) {
            $mail->to($this->email)
                 ->subject($this->subject);
        });
    }

    public function failed(\Throwable $exception): void
    {
        \Log::error("Error to send email {$this->subject} to {$this->email}: " . $exception->getMessage());
    }
}
