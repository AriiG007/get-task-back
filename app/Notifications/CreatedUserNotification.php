<?php

namespace App\Notifications;

use App\Jobs\MailNotification;
use Illuminate\Support\Facades\Log;

class CreatedUserNotification
{

    public static function  sendMail(string $email, string $password): void
    {
        $subject = "GETTASK APP - Your account has been created in the GETTASK application";
        $message = "An administrator created your account. Your password to access the system is: {$password}";

        Log::info("Created User Notification", ['email' => $email, 'subject' => $subject, 'message' => $message ]);

        MailNotification::dispatch(
            $email,
            $subject,
            $message
        )->onQueue('emails');

    }
}
