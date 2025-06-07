<?php

namespace App\Notifications;

use App\Jobs\MailNotification;
use Illuminate\Support\Facades\Log;

class TaskCompletedNotification
{

    public static function sendMail(string $email, string $task, string $completedBy): void
    {
        $subject = "GETTASK APP - Task completed";
        $message = "The task <b> {$task} </b> has been marked as complete by {$completedBy}";

        Log::info("Task Completed Notification", ['email' => $email, 'subject' => $subject, 'message' => $message ]);

        MailNotification::dispatch(
            $email,
            $subject,
            $message
        )->onQueue('emails');

    }
}
