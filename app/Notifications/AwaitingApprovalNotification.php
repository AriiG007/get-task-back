<?php

namespace App\Notifications;

use App\Jobs\MailNotification;
use Illuminate\Support\Facades\Log;

class AwaitingApprovalNotification
{
    public static function sendMail($email)
    {
        $subject = "GETTASK APP - Your account is pending validation";
        $message = "Thank you for registering. An administrator will review your request.";

        Log::info("Awaiting Approval Notification", ['email' => $email, 'subject' => $subject, 'message' => $message ]);


         MailNotification::dispatch(
            $email,
            $subject,
            $message
        )->onQueue('emails');



    }
}
