<?php

namespace Martin6363\ApiAuth\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;

class EmailVerificationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $actionUrl;

    public function __construct(string $actionUrl)
    {
        $this->actionUrl = $actionUrl;

        // Set queue connection and name based on configuration
        $dispatchMode = Config::get('api-auth.emails.dispatch_mode', 'queue');

        if ($dispatchMode === 'sync') {
            $this->connection = 'sync';
        } else {
            $this->connection = config('queue.default');
            $this->queue = Config::get('api-auth.emails.queue_name', 'default');
        }
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $subject = Lang::get('api-auth::auth.verify_email_subject') ?: 'Verify Your Email Address';
        $greeting = Lang::get('api-auth::auth.verify_email_greeting') ?: 'Hello!';
        $line1 = Lang::get('api-auth::auth.verify_email_line_1') ?: 'Please click the button below to verify your email address.';
        $line2 = Lang::get('api-auth::auth.verify_email_line_2') ?: 'This link will expire in 60 minutes.';
        $actionText = Lang::get('api-auth::auth.verify_email_action') ?: 'Verify Email';

        return (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->view('api-auth::emails.email-verification', [
                'lines' => [$line1, $line2],
                'actionText' => $actionText,
                'actionUrl' => $this->actionUrl,
                'primaryColor' => Config::get('api-auth.emails.theme.primary_color', '#4f46e5'),
                'buttonTextColor' => Config::get('api-auth.emails.theme.button_text_color', '#ffffff'),
            ]);
    }
}
