<?php

namespace App\Notifications;

use App\Jobs\MailNotification;
use Illuminate\Support\Facades\Log;

class ResetPasswordUserNotification
{

    public static function sendMail(string $email, string $password): void
    {
        $subject = "GETTASK APP - Your password has been reset in the GETTASK application";
        $message = "An administrator reset your password. Your new password is: {$password}";

        Log::info("Reset Password Notification", [ 'subject' => $subject, 'message' => $message ]);

        MailNotification::dispatch(
            $email,
            $subject,
            $message
        )->onQueue('emails');

    }
}
