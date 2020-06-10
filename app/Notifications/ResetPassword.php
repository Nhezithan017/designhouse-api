<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Auth\Notifications\ResetPassword as Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class ResetPassword extends Notification
{
    public function toMail($notifiable)
    {
        $url = url(config('app.client_url') . '/password/reset/' . $this->token) .
                '?email=' . urlencode($notifiable->email);
        return (new MailMessage)
            ->line('you arereceiving this email because we recieved a password reset request for your account')
            ->action('Reset Password', $url)
            ->line('If you did not  request a password reset, no further action is required.');
    }
}
